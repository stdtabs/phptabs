<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Midi;

use PhpTabs\Music\Beat;
use PhpTabs\Music\Channel;
use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\EffectTremoloBar;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Note;
use PhpTabs\Music\Song;
use PhpTabs\Music\Stroke;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Track;
use PhpTabs\Music\Velocities;
use PhpTabs\Music\Voice;

class MidiSequenceParser
{
    public const DEFAULT_METRONOME_KEY = 37;
    public const DEFAULT_DURATION_PM = 60;
    public const DEFAULT_DURATION_DEAD = 30;
    public const DEFAULT_BEND = 64;
    public const DEFAULT_BEND_SEMI_TONE = 2.75;

    private $song;
    private $flags;
    private $infoTrack;
    private $metronomeTrack;
    private $metronomeChannelId;
    private $firstTickMove;
    private $tempoPercent;
    private $transpose;
    private $sHeader;
    private $eHeader;

    public function __construct(Song $song, int $flags)
    {
        $this->song = $song;
        $this->flags = $flags;
        $this->tempoPercent = 100;
        $this->transpose = 0;
        $this->sHeader = -1;
        $this->eHeader = -1;
        $this->firstTickMove = ($flags & 0x08) != 0
            ? -Duration::QUARTER_TIME
            : 0;
    }

    public function getInfoTrack(): int
    {
        return $this->infoTrack;
    }

    public function getMetronomeTrack(): int
    {
        return $this->metronomeTrack;
    }

    private function getTick(int $tick): int
    {
        return $tick + $this->firstTickMove;
    }

    public function setSHeader(int $header): void
    {
        $this->sHeader = $header;
    }

    public function setEHeader(int $header): void
    {
        $this->eHeader = $header;
    }

    public function setMetronomeChannelId(int $metronomeChannelId): void
    {
        $this->metronomeChannelId = $metronomeChannelId;
    }

    public function setTempoPercent(int $tempoPercent): void
    {
        $this->tempoPercent = $tempoPercent;
    }

    public function setTranspose(int $transpose): void
    {
        $this->transpose = $transpose;
    }

    /**
     * Get a value between 0 and 127
     */
    private function fix(int $value): int
    {
        return min(max(0, $value), 127);
    }

    public function parse(MidiSequenceHandler $sequence): void
    {
        $this->infoTrack = 0;
        $this->metronomeTrack = $sequence->getTracks() - 1;

        $helper = new MidiSequenceHelper($sequence);
        $controller = new MidiRepeatController($this->song, $this->sHeader, $this->eHeader);

        while (!$controller->finished()) {
            $index = $controller->getIndex();
            $move = $controller->getRepeatMove();
            $controller->process();

            if ($controller->shouldPlay()) {
                $helper->addMeasureHelper(new MidiMeasureHelper($index, $move));
            }
        }

        $this->addDefaultMessages($helper, $this->song);

        for ($i = 0; $i < $this->song->countTracks(); $i++) {
            $songTrack = $this->song->getTrack($i);
            $this->createTrack($helper, $songTrack);
        }

        $sequence->notifyFinish();
    }

    private function createTrack(MidiSequenceHelper $helper, Track $track): void
    {
        $channel = $this->song->getChannelById($track->getChannelId());

        if ($channel !== null) {
            $previous = null;

            $this->addBend($helper, $track->getNumber(), Duration::QUARTER_TIME, self::DEFAULT_BEND, $track->getChannelId(), false);
            $this->makeChannel($helper, $channel, $track->getNumber());

            $mCount = count($helper->getMeasureHelpers());

            for ($mIndex = 0; $mIndex < $mCount; $mIndex++) {
                $measureHelper = $helper->getMeasureHelper($mIndex);

                $measure = $track->getMeasure($measureHelper->getIndex());

                if ($track->getNumber() == 1) {
                    $this->addTimeSignature($helper, $measure, $previous, $measureHelper->getMove());
                    $this->addTempo($helper, $measure, $previous, $measureHelper->getMove());
                    $this->addMetronome($helper, $measure->getHeader(), $measureHelper->getMove());
                }

                $this->makeBeats($helper, $channel, $track, $measure, $mIndex, $measureHelper->getMove());

                $previous = $measure;
            }
        }
    }

    private function makeBeats(MidiSequenceHelper $helper, Channel $channel, Track $track, Measure $measure, int $mIndex, int $startMove): void
    {
        $stroke = [];

        for ($i = 0; $i < $track->countStrings(); $i++) {
            $stroke[] = 0;
        }

        $track->getStrings();
        $previous = null;

        for ($bIndex = 0; $bIndex < $measure->countBeats(); $bIndex++) {
            $beat = $measure->getBeat($bIndex);
            $this->makeNotes($helper, $channel, $track, $beat, $measure->getTempo(), $mIndex, $bIndex, $startMove, $this->getStroke($beat, $previous, $stroke));
            $previous = $beat;
        }
    }

    private function makeNotes(
        MidiSequenceHelper $sHelper, Channel $channel, Track $track, Beat $beat,
        Tempo $tempo, int $mIndex, int $bIndex, int $startMove, array $stroke
    ): void {
        for ($vIndex = 0; $vIndex < $beat->countVoices(); $vIndex++) {
            $voice = $beat->getVoice($vIndex);

            $tickHelper = $this->checkTripletFeel($voice, $bIndex);
            for ($noteIdx = 0; $noteIdx < $voice->countNotes(); $noteIdx++) {
                $note = $voice->getNote($noteIdx);
                if (!$note->isTiedNote()) {
                    $key = ($this->transpose + $track->getOffset() + $note->getValue() + $track->getStrings()[$note->getString() - 1]->getValue());

                    $start = $this->applyStrokeStart($note, ($tickHelper->getStart() + $startMove), $stroke);
                    $duration = $this->applyStrokeDuration($note, $this->getRealNoteDuration($sHelper, $track, $note, $tempo, $tickHelper->getDuration(), $mIndex, $bIndex), $stroke);

                    $velocity = $this->getRealVelocity($sHelper, $note, $track, $channel, $mIndex, $bIndex);
                    $channelId = $channel->getId();
                    $midiVoice = $note->getString();
                    $bendMode = false;

                    $percussionChannel = $channel->isPercussionChannel();
                    //---Fade In---
                    if ($note->getEffect()->isFadeIn()) {
                        $this->makeFadeIn($sHelper, $track->getNumber(), $start, $duration, $channelId);
                    }
                    //---Grace---
                    if ($note->getEffect()->isGrace() && !$percussionChannel ) {
                        $bendMode = true;
                        $graceKey = $track->getOffset() + $note->getEffect()->getGrace()->getFret() + $track->getStrings()[$note->getString() - 1]->getValue();
                        $graceLength = $note->getEffect()->getGrace()->getDurationTime();
                        $graceVelocity = $note->getEffect()->getGrace()->getDynamic();
                        $graceDuration = $note->getEffect()->getGrace()->isDead()
                            ? $this->applyStaticDuration($tempo, self::DEFAULT_DURATION_DEAD, $graceLength)
                            : $graceLength;

                        if ($note->getEffect()->getGrace()->isOnBeat() || ($start - $graceLength) < Duration::QUARTER_TIME) {
                            $start += $graceLength;
                            $duration -= $graceLength;
                        }
                        $this->makeNote($sHelper, $track->getNumber(), $graceKey, $start - $graceLength, $graceDuration, $graceVelocity, $channelId, $bendMode);

                    }
                    //---Trill---
                    if ($note->getEffect()->isTrill() && !$percussionChannel ) {
                        $trillKey = $track->getOffset() + $note->getEffect()->getTrill()->getFret() + $track->getStrings()[$note->getString() - 1]->getValue();
                        $trillLength = $note->getEffect()->getTrill()->getDuration()->getTime();

                        $realKey = true;
                        $tick = $start;
                        while (true) {
                            if ($tick + 10 >= ($start + $duration)) {
                                break;
                            } elseif (($tick + $trillLength) >= ($start + $duration)) {
                                $trillLength = ((($start + $duration) - $tick) - 1);
                            }
                            $this->makeNote($sHelper, $track->getNumber(), ($realKey ? $key : $trillKey), $tick, $trillLength, $velocity, $channelId, $bendMode);
                            $realKey = !$realKey;
                            $tick += $trillLength;
                        }

                        continue;
                    }
                    //---Tremolo Picking---
                    if ($note->getEffect()->isTremoloPicking()) {
                        $tpLength = $note->getEffect()->getTremoloPicking()->getDuration()->getTime();
                        $tick = $start;
                        while (true) {
                            if ($tick + 10 >= ($start + $duration)) {
                                break;
                            } elseif (($tick + $tpLength) >= ($start + $duration)) {
                                      $tpLength = ((($start + $duration) - $tick) - 1);
                            }
                            $this->makeNote($sHelper, $track->getNumber(), $key, $tick, $tpLength, $velocity, $channelId, $bendMode);
                            $tick += $tpLength;
                        }
                        continue;
                    }

                    //---Bend---
                    if ($note->getEffect()->isBend() && !$percussionChannel) {
                        $bendMode = true;
                        $this->makeBend($sHelper, $track->getNumber(), $start, $duration, $note->getEffect()->getBend(), $channelId, $midiVoice, $bendMode);
                    }
                    //---TremoloBar---
                    elseif ($note->getEffect()->isTremoloBar() && !$percussionChannel) {
                        $bendMode = true;
                        $this->makeTremoloBar($sHelper, $track->getNumber(), $start, $duration, $note->getEffect()->getTremoloBar(), $channelId, $midiVoice, $bendMode);
                    }
                    //---Slide---
                    elseif ($note->getEffect()->isSlide() && !$percussionChannel) {
                        $bendMode = true;
                        $this->makeSlide($sHelper, $note, $track, $mIndex, $bIndex, $startMove, $channelId, $midiVoice, $bendMode);
                    }
                    //---Vibrato---
                    elseif ($note->getEffect()->isVibrato() && !$percussionChannel) {
                        $bendMode = true;
                        $this->makeVibrato($sHelper, $track->getNumber(), $start, $duration, $channelId, $midiVoice, $bendMode);
                    }
                    //---Harmonic---
                    if ($note->getEffect()->isHarmonic() && !$percussionChannel) {
                        $orig = $key;

                        //Natural
                        if ($note->getEffect()->getHarmonic()->isNatural()) {
                            for ($i = 0; $i < count(EffectHarmonic::NATURAL_FREQUENCIES); $i++) {
                                if (($note->getValue() % 12) ==  (EffectHarmonic::NATURAL_FREQUENCIES[$i][0] % 12)) {
                                    $key = $orig + EffectHarmonic::NATURAL_FREQUENCIES[$i][1] - $note->getValue();
                                    break;
                                }
                            }
                        }
                        //Artifical/Tapped/Pinch/Semi
                        else {
                            if ($note->getEffect()->getHarmonic()->isSemi() && !$percussionChannel) {
                                $this->makeNote($sHelper, $track->getNumber(), min(127, $orig), $start, $duration, max(Velocities::MIN_VELOCITY, $velocity - (Velocities::VELOCITY_INCREMENT * 3)), $channelId, $bendMode);
                            }
                            $key = ($orig + EffectHarmonic::NATURAL_FREQUENCIES[$note->getEffect()->getHarmonic()->getData()][1]);

                        }
                        if (($key - 12) > 0) {
                            $hVelocity = max(Velocities::MIN_VELOCITY, $velocity - (Velocities::VELOCITY_INCREMENT * 4));
                            $this->makeNote($sHelper, $track->getNumber(), ($key - 12), $start, $duration, $hVelocity, $channelId, $bendMode);
                        }
                    }

                    //---Normal Note---
                    $this->makeNote($sHelper, $track->getNumber(), min(127, $key), $start, $duration, $velocity, $channelId, $bendMode);
                }
            }
        }
    }

    private function makeNote(MidiSequenceHelper $sHelper, int $track, int $key, int $start, int $duration, int $velocity, int $channel, bool $bendMode): void
    {
        $sHelper->getSequence()->addNoteOn($this->getTick($start), $track, $channel, $this->fix($key), $this->fix($velocity), $bendMode);

        if ($duration > 0) {
            $sHelper->getSequence()->addNoteOff($this->getTick($start + $duration), $track, $channel, $this->fix($key), $this->fix($velocity), $bendMode);
        }
    }

    private function makeChannel(MidiSequenceHelper $sHelper, Channel $channel, int $track): void
    {
        if (($this->flags & MidiWriter::ADD_MIXER_MESSAGES) != 0) {
            $channelId = $channel->getId();
            $tick = $this->getTick(Duration::QUARTER_TIME);
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::VOLUME, $this->fix($channel->getVolume()));
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::BALANCE, $this->fix($channel->getBalance()));
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::CHORUS, $this->fix($channel->getChorus()));
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::REVERB, $this->fix($channel->getReverb()));
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::PHASER, $this->fix($channel->getPhaser()));
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::TREMOLO, $this->fix($channel->getTremolo()));
            $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::EXPRESSION, 127);

            if (!$channel->isPercussionChannel()) {
                $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::BANK_SELECT, $this->fix($channel->getBank()));
            }

            $sHelper->getSequence()->addProgramChange($tick, $track, $channelId, $this->fix($channel->getProgram()));
        }
    }

    private function addTimeSignature(MidiSequenceHelper $sHelper, Measure $currMeasure, Measure $prevMeasure = null, int $startMove = 0): void
    {
        $addTimeSignature = false;

        if ($prevMeasure === null) {
            $addTimeSignature = true;
        } else {
            $currNumerator = $currMeasure->getTimeSignature()->getNumerator();
            $currValue = $currMeasure->getTimeSignature()->getDenominator()->getValue();
            $prevNumerator = $prevMeasure->getTimeSignature()->getNumerator();
            $prevValue = $prevMeasure->getTimeSignature()->getDenominator()->getValue();
            if ($currNumerator != $prevNumerator || $currValue != $prevValue) {
                $addTimeSignature = true;
            }
        }

        if ($addTimeSignature) {
            $sHelper->getSequence()->addTimeSignature($this->getTick($currMeasure->getStart() + $startMove), $this->getInfoTrack(), $currMeasure->getTimeSignature());
        }
    }

    private function addTempo(MidiSequenceHelper $sHelper, Measure $currMeasure, Measure $prevMeasure = null, int $startMove = 0): void
    {
        $addTempo = false;
        if ($prevMeasure === null) {
            $addTempo = true;
        } else {
            if ($currMeasure->getTempo()->getInTPQ() != $prevMeasure->getTempo()->getInTPQ()) {
                $addTempo = true;
            }
        }
        if ($addTempo) {
            $usq = ($currMeasure->getTempo()->getInTPQ() * 100 / $this->tempoPercent );
            $sHelper->getSequence()->addTempoInUSQ($this->getTick($currMeasure->getStart() + $startMove), $this->getInfoTrack(), $usq);
        }
    }

    private function getRealNoteDuration(MidiSequenceHelper $sHelper, Track $track, Note $note, Tempo $tempo, int $duration, int $mIndex, int $bIndex): int
    {
        $letRing = ($note->getEffect()->isLetRing());
        $letRingBeatChanged = false;
        $lastEnd = ($note->getVoice()->getBeat()->getStart() + $note->getVoice()->getDuration()->getTime() + $sHelper->getMeasureHelper($mIndex)->getMove());
        $realDuration = $duration;
        $nextBIndex = ($bIndex + 1);
        $mCount = count($sHelper->getMeasureHelpers());
        for ($m = $mIndex; $m < $mCount; $m++) {
            $mh = $sHelper->getMeasureHelper($m);
            $measure = $track->getMeasure($mh->getIndex());

            $beatCount = $measure->countBeats();
            for ($b = $nextBIndex; $b < $beatCount; $b++) {
                $beat = $measure->getBeat($b);
                $voice = $beat->getVoice($note->getVoice()->getIndex());
                if (!$voice->isEmpty()) {
                    if ($voice->isRestVoice()) {
                        return $this->applyDurationEffects($note, $tempo, $realDuration);
                    }
                    $noteCount = $voice->countNotes();
                    for ($n = 0; $n < $noteCount; $n++) {
                        $nextNote = $voice->getNote($n);
                        if (!$nextNote == $note || $mIndex != $m) {
                            if ($nextNote->getString() == $note->getString()) {
                                if ($nextNote->isTiedNote()) {
                                    $realDuration += $mh->getMove() + $beat->getStart() - $lastEnd + $nextNote->getVoice()->getDuration()->getTime();
                                    $lastEnd = $mh->getMove() + $beat->getStart() + $voice->getDuration()->getTime();
                                    $letRing = $nextNote->getEffect()->isLetRing();
                                    $letRingBeatChanged = true;
                                } else {
                                          return $this->applyDurationEffects($note, $tempo, $realDuration);
                                }
                            }
                        }
                    }

                    if ($letRing && !$letRingBeatChanged) {
                        $realDuration += $voice->getDuration()->getTime();
                    }
                    $letRingBeatChanged = false;
                }
            }
            $nextBIndex = 0;
        }

        return $this->applyDurationEffects($note, $tempo, $realDuration);
    }

    private function applyDurationEffects(Note $note, Tempo $tempo, int $duration): int
    {
        //dead note
        if ($note->getEffect()->isDeadNote()) {
            return $this->applyStaticDuration($tempo, self::DEFAULT_DURATION_DEAD, $duration);
        }

        //palm mute
        if ($note->getEffect()->isPalmMute()) {
            return $this->applyStaticDuration($tempo, self::DEFAULT_DURATION_PM, $duration);
        }

        //staccato
        if ($note->getEffect()->isStaccato()) {
            return intval($duration * 50 / 100);
        }

        return $duration;
    }

    private function applyStaticDuration(Tempo $tempo, int $duration, int $maximum): int
    {
        $value = $tempo->getValue() * $duration / 60;

        return $value < $maximum
            ? intval($value)
            : $maximum;
    }

    private function getRealVelocity(MidiSequenceHelper $sHelper, Note $note, Track $track, Channel $channel, int $mIndex, int $bIndex): int
    {
        $velocity = $note->getVelocity();

        //Check for Hammer effect
        if (!$channel->isPercussionChannel()) {
            $previousNote = $this->getPreviousNote($sHelper, $note, $track, $mIndex, $bIndex, false);
            if ($previousNote !== null && $previousNote->getNote()->getEffect()->isHammer()) {
                $velocity = max(Velocities::MIN_VELOCITY, $velocity - 25);
            }
        }

        //Check for GhostNote effect
        if ($note->getEffect()->isGhostNote()) {
            $velocity = max(Velocities::MIN_VELOCITY, $velocity - Velocities::VELOCITY_INCREMENT);
        } elseif ($note->getEffect()->isAccentuatedNote()) {
            $velocity = max(Velocities::MIN_VELOCITY, $velocity + Velocities::VELOCITY_INCREMENT);
        } elseif ($note->getEffect()->isHeavyAccentuatedNote()) {
            $velocity = max(Velocities::MIN_VELOCITY, $velocity + (Velocities::VELOCITY_INCREMENT * 2));
        }

        return $velocity > 127 ? 127 : $velocity;
    }

    public function addMetronome(MidiSequenceHelper $sHelper, MeasureHeader $header, int $startMove): void
    {
        if (($this->flags & MidiWriter::ADD_METRONOME) != 0) {
            if ($this->metronomeChannelId >= 0) {
                $start = $startMove + $header->getStart();
                $length = $header->getTimeSignature()->getDenominator()->getTime();
                for ($i = 1; $i <= $header->getTimeSignature()->getNumerator(); $i++) {
                    $this->makeNote($sHelper, $this->getMetronomeTrack(), self::DEFAULT_METRONOME_KEY, $start, $length, Velocities::_DEFAULT, $this->metronomeChannelId, false);
                    $start += $length;
                }
            }
        }
    }

    public function addDefaultMessages(MidiSequenceHelper $sHelper, Song $song): void
    {
        if (($this->flags & MidiWriter::ADD_DEFAULT_CONTROLS) != 0) {
            $channels = $song->getChannels();
            foreach ($channels as $channel) {
                $channelId = $channel->getId();
                $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::RPN_MSB, 0);
                $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::RPN_LSB, 0);
                $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::DATA_ENTRY_MSB, 12);
                $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::DATA_ENTRY_LSB, 0);
            }
        }
    }

    private function addBend(MidiSequenceHelper $sHelper, int $track, int $tick, int $bend, int $channel, bool $bendMode): void
    {
        $sHelper->getSequence()->addPitchBend($this->getTick($tick), $track, $channel, $this->fix($bend), $bendMode);
    }

    public function makeVibrato(MidiSequenceHelper $sHelper, int $track, int $start, int $duration, int $channel, int $midiVoice, bool $bendMode): void
    {
        $nextStart = $start;
        $end = $nextStart + $duration;

        while ($nextStart < $end) {
            $nextStart = ($nextStart + 160 > $end)
                ? $end
                : ($nextStart + 160);

            $this->addBend($sHelper, $track, $nextStart, self::DEFAULT_BEND, $channel, $bendMode);

            $nextStart = ($nextStart + 160 > $end)
                ? $end
                : ($nextStart + 160);

            $this->addBend($sHelper, $track, $nextStart, self::DEFAULT_BEND + intval(self::DEFAULT_BEND_SEMI_TONE / 2), $channel, $bendMode);
        }

        $this->addBend($sHelper, $track, $nextStart, self::DEFAULT_BEND, $channel, $bendMode);
    }

    public function makeBend(MidiSequenceHelper $sHelper, int $track, int $start, int $duration, EffectBend $bend, int $channel, int $midiVoice, bool $bendMode): void
    {
        $points = $bend->getPoints();
        for ($i = 0; $i < count($points); $i++) {
            $point = $points[$i];
            $bendStart = $start + $point->getTime($duration);
            $value = self::DEFAULT_BEND + intval($point->getValue() * self::DEFAULT_BEND_SEMI_TONE / EffectBend::SEMITONE_LENGTH);
            $value = $value <= 127 ? $value : 127;
            $value = $value >= 0 ? $value : 0;
            $this->addBend($sHelper, $track, $bendStart, $value, $channel, $bendMode);

            if (count($points) > $i + 1) {
                $nextPoint = $points[$i + 1];
                $nextValue = self::DEFAULT_BEND + intval($nextPoint->getValue() * self::DEFAULT_BEND_SEMI_TONE / EffectBend::SEMITONE_LENGTH);
                $nextBendStart = $start + $nextPoint->getTime($duration);
                if ($nextValue != $value) {
                    $width = ($nextBendStart - $bendStart) / abs($nextValue - $value);
                    //asc
                    if ($value < $nextValue) {
                        while ($value < $nextValue) {
                            $value++;
                            $bendStart += $width;
                            $this->addBend($sHelper, $track, intval($bendStart), ($value <= 127 ? $value : 127), $channel, $bendMode);
                        }
                        //desc
                    } elseif ($value > $nextValue) {
                        while ($value > $nextValue) {
                              $value--;
                              $bendStart += $width;
                              $this->addBend($sHelper, $track, $bendStart, ($value >= 0 ? $value : 0), $channel, $bendMode);
                        }
                    }
                }
            }
        }
        $this->addBend($sHelper, $track, $start + $duration, self::DEFAULT_BEND, $channel, $bendMode);
    }

    public function makeTremoloBar(MidiSequenceHelper $sHelper, int $track, int $start, int $duration, EffectTremoloBar $effect, int $channel, int $midiVoice, bool $bendMode): void
    {
        $points = $effect->getPoints();
        for ($i = 0; $i < count($points); $i++) {
            $point = $points[$i];
            $pointStart = $start + $point->getTime($duration);
            $value = self::DEFAULT_BEND + intval($point->getValue() * self::DEFAULT_BEND_SEMI_TONE * 2);
            $value = $value <= 127 ? $value : 127;
            $value = $value >= 0 ? $value : 0;
            $this->addBend($sHelper, $track, $pointStart, $value, $channel, $bendMode);
            if (count($points) > $i + 1) {
                $nextPoint = $points[$i + 1];
                $nextValue = self::DEFAULT_BEND + intval($nextPoint->getValue() * self::DEFAULT_BEND_SEMI_TONE * 2);
                $nextPointStart = $start + $nextPoint->getTime($duration);
                if ($nextValue != $value) {
                    $width = ($nextPointStart - $pointStart) / abs($nextValue - $value);
                    //asc
                    if ($value < $nextValue) {
                        while ($value < $nextValue) {
                            $value++;
                            $pointStart += $width;
                            $this->addBend($sHelper, $track, intval($pointStart), ($value <= 127 ? $value : 127), $channel, $bendMode);
                        }
                        //desc
                    } elseif ($value > $nextValue) {
                        while ($value > $nextValue) {
                              $value--;
                              $pointStart += $width;
                              $this->addBend($sHelper, $track, intval($pointStart), ($value >= 0 ? $value : 0), $channel, $bendMode);
                        }
                    }
                }
            }
        }
        $this->addBend($sHelper, $track, $start + $duration, self::DEFAULT_BEND, $channel, $bendMode);
    }

    private function makeSlide(MidiSequenceHelper $sHelper, Note $note, Track $track, int $mIndex, int $bIndex, int $startMove, int $channel, int $midiVoice, bool $bendMode): void
    {
        $nextNote = $this->getNextNote($sHelper, $note, $track, $mIndex, $bIndex, true);
        if ($nextNote !== null) {
            $value1 = $note->getValue();
            $value2 = $nextNote->getNote()->getValue();

            $tick1 = $note->getVoice()->getBeat()->getStart() + $startMove;
            $tick2 = $nextNote->getNote()->getVoice()->getBeat()->getStart() + $nextNote->getMeasure()->getMove();

            // Make the Slide
            $this->makeMidiSlide($sHelper, $track->getNumber(), $tick1, $value1, $tick2, $value2, $channel, $midiVoice, $bendMode);
            // Normalize the Bend
            $this->addBend($sHelper, $track->getNumber(), $tick2, self::DEFAULT_BEND, $channel, $bendMode);
        }
    }

    public function makeMidiSlide(MidiSequenceHelper $sHelper, int $track, int $tick1, int $value1, int $tick2, int $value2, int $channel, int $midiVoice, bool $bendMode): void
    {
        $distance = $value2 - $value1;
        $length = $tick2 - $tick1;
        $points = intval($length / (Duration::QUARTER_TIME / 8));
        for ($i = 1; $i <= $points; $i++) {
            $tone = (((($length / $points) * $i) * $distance) / $length);
            $bend = self::DEFAULT_BEND + intval($tone * (self::DEFAULT_BEND_SEMI_TONE * 2));
            $this->addBend($sHelper, $track, $tick1 + ( ($length / $points) * $i), $bend, $channel, $bendMode);
        }
    }

    private function makeFadeIn(MidiSequenceHelper $sHelper, int $track, int $start, int $duration, int $channel): void
    {
        $expression = 31;
        $expressionIncrement = 1;
        $tick = $start;
        $tickIncrement = intval($duration / ((127 - $expression) / $expressionIncrement));
        while ($tick < ($start + $duration) && $expression < 127) {
            $sHelper->getSequence()->addControlChange($this->getTick($tick), $track, $channel, MidiWriter::EXPRESSION, $this->fix($expression));
            $tick += $tickIncrement;
            $expression += $expressionIncrement;
        }
        $sHelper->getSequence()->addControlChange($this->getTick(($start + $duration)), $track, $channel, MidiWriter::EXPRESSION, 127);
    }

    private function getStroke(Beat $beat, Beat $previous = null, array $stroke): array
    {
        $direction = $beat->getStroke()->getDirection();
        if ($previous === null || !($direction == Stroke::STROKE_NONE
            && $previous->getStroke()->getDirection() == Stroke::STROKE_NONE)
        ) {
            if ($direction == Stroke::STROKE_NONE) {
                for ($i = 0; $i < count($stroke); $i++) {
                    $stroke[ $i ] = 0;
                }
            } else {
                $stringUseds = 0;
                $stringCount = 0;
                for ($vIndex = 0; $vIndex < $beat->countVoices(); $vIndex++) {
                    $voice = $beat->getVoice($vIndex);
                    for ($nIndex = 0; $nIndex < $voice->countNotes(); $nIndex++) {
                        $note = $voice->getNote($nIndex);
                        if (!$note->isTiedNote()) {
                            $stringUseds |= 0x01 << ( $note->getString() - 1 );
                            $stringCount ++;
                        }
                    }
                }
                if ($stringCount > 0) {
                    $strokeMove = 0;
                    $strokeIncrement = $beat->getStroke()->getIncrementTime($beat);
                    for ($i = 0; $i < count($stroke); $i++) {
                        $index = ( $direction == Stroke::STROKE_DOWN ? (count($stroke) - 1) - $i : $i );
                        if (($stringUseds & ( 0x01 << $index ) ) != 0) {
                              $stroke[ $index ] = $strokeMove;
                              $strokeMove += $strokeIncrement;
                        }
                    }
                }
            }
        }

        return $stroke;
    }

    private function applyStrokeStart(Note $note, int $start, array $stroke): int
    {
        return ($start + $stroke[$note->getString() - 1]);
    }

    private function applyStrokeDuration(Note $note, int $duration, array $stroke): int
    {
        return $duration > $stroke[$note->getString() - 1]
            ? ($duration - $stroke[$note->getString() - 1])
            : $duration;
    }

    private function checkTripletFeel(Voice $voice, int $bIndex): MidiTickHelper
    {
        $bStart = $voice->getBeat()->getStart();
        $bDuration =  $voice->getDuration()->getTime();
        if ($voice->getBeat()->getMeasure()->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_EIGHTH) {
            if ($voice->getDuration()->isEqual($this->newDuration(Duration::EIGHTH))) {
                //first time
                if (($bStart % Duration::QUARTER_TIME) == 0) {
                    $v = $this->getNextBeat($voice, $bIndex);
                    if ($v === null || ( $v->getBeat()->getStart() > ($bStart + $voice->getDuration()->getTime()) || $v->getDuration()->isEqual($this->newDuration(Duration::EIGHTH)))) {
                        $duration = $this->newDuration(Duration::EIGHTH);
                        $duration->getDivision()->setEnters(3);
                        $duration->getDivision()->setTimes(2);
                        $bDuration = ($duration->getTime() * 2);
                    }
                }
                //second time
                elseif (($bStart % (Duration::QUARTER_TIME / 2)) == 0) {
                    $v = $this->getPreviousBeat($voice, $bIndex);
                    if ($v === null || ( $v->getBeat()->getStart() < ($bStart - $voice->getDuration()->getTime()) || $v->getDuration()->isEqual($this->newDuration(Duration::EIGHTH)))) {
                        $duration = $this->newDuration(Duration::EIGHTH);
                        $duration->getDivision()->setEnters(3);
                        $duration->getDivision()->setTimes(2);
                        $bStart = ( ($bStart - $voice->getDuration()->getTime()) + ($duration->getTime() * 2));
                        $bDuration = $duration->getTime();
                    }
                }
            }
        } elseif ($voice->getBeat()->getMeasure()->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_SIXTEENTH) {
            if ($voice->getDuration()->isEqual($this->newDuration(Duration::SIXTEENTH))) {
                //first time
                if (($bStart % (Duration::QUARTER_TIME / 2)) == 0) {
                    $v = $this->getNextBeat($voice, $bIndex);
                    if ($v === null || ( $v->getBeat()->getStart() > ($bStart + $voice->getDuration()->getTime()) || $v->getDuration()->isEqual($this->newDuration(Duration::SIXTEENTH)))) {
                        $duration = $this->newDuration(Duration::SIXTEENTH);
                        $duration->getDivision()->setEnters(3);
                        $duration->getDivision()->setTimes(2);
                        $bDuration = ($duration->getTime() * 2);
                    }
                }
                //second time
                elseif (($bStart % (Duration::QUARTER_TIME / 4)) == 0) {
                    $v = $this->getPreviousBeat($voice, $bIndex);
                    if ($v === null || ( $v->getBeat()->getStart() < ($bStart - $voice->getDuration()->getTime()) || $v->getDuration()->isEqual($this->newDuration(Duration::SIXTEENTH)))) {
                        $duration = $this->newDuration(Duration::SIXTEENTH);
                        $duration->getDivision()->setEnters(3);
                        $duration->getDivision()->setTimes(2);
                        $bStart = (($bStart - $voice->getDuration()->getTime()) + ($duration->getTime() * 2));
                        $bDuration = $duration->getTime();
                    }
                }
            }
        }

        return new MidiTickHelper($bStart, $bDuration);
    }

    private function newDuration(int $value): Duration
    {
        $duration = new Duration();
        $duration->setValue($value);

        return $duration;
    }

    private function getPreviousBeat(Voice $voice, int $bIndex): ?Voice
    {
        $previous = null;

        for ($b = $bIndex - 1; $b >= 0; $b--) {
            $current = $voice->getBeat()->getMeasure()->getBeat($b);
            if ($current->getStart() < $voice->getBeat()->getStart() && !$current->getVoice($voice->getIndex())->isEmpty()) {
                if ($previous === null || $current->getStart() > $previous->getBeat()->getStart()) {
                    $previous = $current->getVoice($voice->getIndex());
                }
            }
        }

        return $previous;
    }

    private function getNextBeat(Voice $voice, int $bIndex): ?Voice
    {
        $next = null;
        for ($b = $bIndex + 1; $b < $voice->getBeat()->getMeasure()->countBeats(); $b++) {
            $current = $voice->getBeat()->getMeasure()->getBeat($b);
            if ($current->getStart() > $voice->getBeat()->getStart() && !$current->getVoice($voice->getIndex())->isEmpty()) {
                if ($next === null || $current->getStart() < $next->getBeat()->getStart()) {
                    $next = $current->getVoice($voice->getIndex());
                }
            }
        }

        return $next;
    }

    private function getNextNote(MidiSequenceHelper $sHelper, Note $note, Track $track, int $mIndex, int $bIndex, bool $breakAtRest): ?MidiNoteHelper
    {
        $nextBIndex = $bIndex + 1;
        $measureCount = count($sHelper->getMeasureHelpers());
        for ($m = $mIndex; $m < $measureCount; $m++) {
            $mHelper = $sHelper->getMeasureHelper($m);

            $measure = $track->getMeasure($mHelper->getIndex());
            $beatCount = $measure->countBeats();
            for ($b = $nextBIndex; $b < $beatCount; $b++) {
                $beat = $measure->getBeat($b);
                $voice = $beat->getVoice($note->getVoice()->getIndex());
                if (!$voice->isEmpty()) {
                    $noteCount = $voice->countNotes();
                    for ($n = 0; $n < $noteCount; $n++) {
                        $nextNote = $voice->getNote($n);
                        if ($nextNote->getString() == $note->getString()) {
                            return new MidiNoteHelper($mHelper, $nextNote);
                        }
                    }
                    if ($breakAtRest) {
                        return null;
                    }
                }
            }
            $nextBIndex = 0;
        }

        return null;
    }

    private function getPreviousNote(MidiSequenceHelper $sHelper, Note $note, Track $track, int $mIndex, int $bIndex, bool $breakAtRest): ?MidiNoteHelper
    {
        $nextBIndex = $bIndex;
        for ($m = $mIndex; $m >= 0; $m--) {
            $mHelper = $sHelper->getMeasureHelper($m);

            $measure = $track->getMeasure($mHelper->getIndex());
            if ($this->sHeader == -1 || $this->sHeader <= $measure->getNumber()) {
                $nextBIndex = $nextBIndex < 0 ? $measure->countBeats() : $nextBIndex;
                for ($b = ($nextBIndex - 1); $b >= 0; $b--) {
                    $beat = $measure->getBeat($b);
                    $voice = $beat->getVoice($note->getVoice()->getIndex());
                    if (!$voice->isEmpty()) {
                        $noteCount = $voice->countNotes();
                        for ($n = 0; $n < $noteCount; $n++)
                        {
                            $current = $voice->getNote($n);
                            if ($current->getString() == $note->getString()) {
                                  return new MidiNoteHelper($mHelper, $current);
                            }
                        }
                        if ($breakAtRest) {
                            return null;
                        }
                    }
                }
            }
            $nextBIndex = -1;
        }

        return null;
    }
}
