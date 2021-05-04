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

namespace PhpTabs\Writer\GuitarPro;

use Exception;
use PhpTabs\Music\Beat;
use PhpTabs\Music\DivisionType;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\Marker;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Note;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Song;
use PhpTabs\Music\Stroke;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Text;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\Music\Velocities;
use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface as GprInterface;
use PhpTabs\Share\MeasureVoiceJoiner;

final class GuitarPro3Writer extends GuitarProWriterBase
{
    private const VERSION = 'FICHIER GUITAR PRO v3.00';

    public function __construct(Song $song)
    {
        parent::__construct();

        if ($song->isEmpty()) {
            throw new Exception('Song is empty');
        }

        $this->configureChannelRouter($song);
        $header = $song->getMeasureHeader(0);
        $this->writeStringByte(self::VERSION, 30);
        $this->writeInformations($song);
        $this->writeBoolean(
            $header->getTripletFeel() === MeasureHeader::TRIPLET_FEEL_EIGHTH
        );
        $this->writeInt($header->getTempo()->getValue());
        $this->writeInt(0);
        $this->getWriter('ChannelWriter')->writeChannels($song);
        $this->writeInt($song->countMeasureHeaders());
        $this->writeInt($song->countTracks());
        $this->writeMeasureHeaders($song);
        $this->writeTracks($song);
        $this->writeMeasures($song, clone $header->getTempo());
    }

    private function writeBeat(Beat $beat, Measure $measure, bool $changeTempo): void
    {
        $voice = $beat->getVoice(0);
        $duration = $voice->getDuration();
        $flags = 0;

        if ($duration->isDotted() || $duration->isDoubleDotted()) {
            $flags |= 0x01;
        }

        if (! $duration->getDivision()->isEqual(DivisionType::normal())) {
            $flags |= 0x20;
        }

        if ($beat->isTextBeat()) {
            $flags |= 0x04;
        }

        if ($changeTempo) {
            $flags |= 0x10;
        }

        $effect = null;
        if ($voice->isRestVoice()) {
            $flags |= 0x40;
        } elseif ($voice->countNotes()) {
            $note = $voice->getNote(0);
            $effect = $note->getEffect();

            if ($effect->isVibrato()
                || $effect->isTremoloBar()
                || $effect->isTapping()
                || $effect->isSlapping()
                || $effect->isPopping()
                || $effect->isHarmonic()
                || $effect->isFadeIn()
                || $beat->getStroke()->getDirection() !== Stroke::STROKE_NONE
            ) {
                $flags |= 0x08;
            }
        }

        $this->writeUnsignedByte($flags);

        if (($flags & 0x40) !== 0) {
            $this->writeUnsignedByte(2);
        }

        $this->writeByte($this->parseDuration($duration));

        if (($flags & 0x20) !== 0) {
            $this->writeInt($duration->getDivision()->getEnters());
        }

        if (($flags & 0x04) !== 0) {
            $this->writeText($beat->getText());
        }

        if (($flags & 0x08) !== 0) {
            $this->writeBeatEffects($beat, $effect);
        }

        if (($flags & 0x10) !== 0) {
            $this->writeMixChange($measure->getTempo());
        }

        $stringFlags = 0;

        if (! $voice->isRestVoice()) {
            foreach ($voice->getNotes() as $playedNote) {
                $string = 7 - $playedNote->getString();
                $stringFlags |= 1 << $string;
            }
        }

        $this->writeUnsignedByte($stringFlags);

        for ($i = 6; $i >= 0; $i--) {
            if (($stringFlags & (1 << $i)) !== 0) {
                foreach ($voice->getNotes() as $playedNote) {
                    if ($playedNote->getString() === 6 - $i + 1) {
                        $this->writeNote($playedNote);
                        break;
                    }
                }
            }
        }
    }

    private function writeBeatEffects(Beat $beat, NoteEffect $noteEffect): void
    {
        $flags = 0;
        if ($noteEffect->isVibrato()) {
            $flags += 0x01;
        }

        if ($noteEffect->isTremoloBar() || $noteEffect->isTapping()
            || $noteEffect->isSlapping() || $noteEffect->isPopping()
        ) {
            $flags += 0x20;
        }

        if ($beat->getStroke()->getDirection() !== Stroke::STROKE_NONE) {
            $flags |= 0x40;
        }

        if ($noteEffect->isHarmonic()
            && $noteEffect->getHarmonic()->getType() === EffectHarmonic::TYPE_NATURAL
        ) {
            $flags += 0x04;
        }

        if ($noteEffect->isHarmonic()
            && $noteEffect->getHarmonic()->getType() !== EffectHarmonic::TYPE_NATURAL
        ) {
            $flags += 0x08;
        }

        if ($noteEffect->isFadeIn()) {
            $flags += 0x10;
        }

        $this->writeUnsignedByte($flags);

        if (($flags & 0x20) !== 0) {
            if ($noteEffect->isTremoloBar()) {
                $this->writeUnsignedByte(0);
                $this->writeInt(100);
            } elseif ($noteEffect->isTapping()) {
                $this->writeUnsignedByte(1);
                $this->writeInt(0);
            } elseif ($noteEffect->isSlapping()) {
                $this->writeUnsignedByte(2);
                $this->writeInt(0);
            } elseif ($noteEffect->isPopping()) {
                $this->writeUnsignedByte(3);
                $this->writeInt(0);
            }
        }

        if (($flags & 0x40) !== 0) {
            $this->writeUnsignedByte(
                $beat->getStroke()->getDirection() === Stroke::STROKE_DOWN
                ? $this->toStrokeValue($beat->getStroke()) : 0
            );
            $this->writeUnsignedByte(
                $beat->getStroke()->getDirection() === Stroke::STROKE_UP
                ? $this->toStrokeValue($beat->getStroke()) : 0
            );
        }
    }

    private function writeBend(EffectBend $bend): void
    {
        $this->writeByte(1);
        $this->writeInt(0);
        $this->writeInt($bend->countPoints());

        foreach ($bend->getPoints() as $point) {
            $this->writeInt(
                intval($point->getPosition() * GprInterface::GP_BEND_POSITION / EffectBend::MAX_POSITION_LENGTH)
            );
            $this->writeInt(
                intval($point->getValue() * GprInterface::GP_BEND_SEMITONE / EffectBend::SEMITONE_LENGTH)
            );
            $this->writeByte(0);
        }
    }

    private function writeGrace(EffectGrace $grace): void
    {
        $grace->isDead()
            ? $this->writeUnsignedByte(255)
            : $this->writeUnsignedByte($grace->getFret());

        $this->writeUnsignedByte(
            intval((($grace->getDynamic() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1)
        );

        switch ($grace->getTransition()) {
            case EffectGrace::TRANSITION_NONE:
                $this->writeUnsignedByte(0);
                break;
            case EffectGrace::TRANSITION_SLIDE:
                $this->writeUnsignedByte(1);
                break;
            case EffectGrace::TRANSITION_BEND:
                $this->writeUnsignedByte(2);
                break;
            case EffectGrace::TRANSITION_HAMMER:
                $this->writeUnsignedByte(3);
                break;
        }

        $this->writeUnsignedByte($grace->getDuration());
    }

    private function writeInformations(Song $song): void
    {
        $this->writeStringByteSizeOfInteger((string) $song->getName());
        $this->writeStringByteSizeOfInteger('');
        $this->writeStringByteSizeOfInteger((string) $song->getArtist());
        $this->writeStringByteSizeOfInteger((string) $song->getAlbum());
        $this->writeStringByteSizeOfInteger((string) $song->getAuthor());
        $this->writeStringByteSizeOfInteger((string) $song->getCopyright());
        $this->writeStringByteSizeOfInteger((string) $song->getWriter());
        $this->writeStringByteSizeOfInteger('');

        $comments = $this->toCommentLines((string) $song->getComments());
        $this->writeInt(count($comments));
        foreach ($comments as $comment) {
            $this->writeStringByteSizeOfInteger($comment);
        }
    }

    private function writeMarker(Marker $marker): void
    {
        $this->writeStringByteSizeOfInteger($marker->getTitle());
        $this->writeColor($marker->getColor());
    }

    private function writeMeasure(Measure $srcMeasure, bool $changeTempo): void
    {
        $measure = (new MeasureVoiceJoiner($srcMeasure))->process();

        $beatCount = $measure->countBeats();
        $this->writeInt($beatCount);

        for ($i = 0; $i < $beatCount; $i++) {
            $beat = $measure->getBeat($i);
            $this->writeBeat($beat, $measure, ($changeTempo && $i === 0));
        }
    }

    private function writeMeasureHeader(MeasureHeader $measure, TimeSignature $timeSignature): void
    {
        $flags = 0;

        if ($measure->getNumber() === 1
            || $measure->getTimeSignature()->getNumerator() !== $timeSignature->getNumerator()
        ) {
            $flags |= 0x01;
        }

        if ($measure->getNumber() === 1
            || $measure->getTimeSignature()->getDenominator()->getValue() !== $timeSignature->getDenominator()->getValue()
        ) {
            $flags |= 0x02;
        }

        if ($measure->isRepeatOpen()) {
            $flags |= 0x04;
        }

        if ($measure->getRepeatClose() > 0) {
            $flags |= 0x08;
        }

        if ($measure->hasMarker()) {
            $flags |= 0x20;
        }

        $this->writeUnsignedByte($flags);

        if (($flags & 0x01) !== 0) {
            $this->writeByte($measure->getTimeSignature()->getNumerator());
        }

        if (($flags & 0x02) !== 0) {
            $this->writeByte($measure->getTimeSignature()->getDenominator()->getValue());
        }

        if (($flags & 0x08) !== 0) {
            $this->writeByte($measure->getRepeatClose());
        }

        if (($flags & 0x20) !== 0) {
            $this->writeMarker($measure->getMarker());
        }
    }

    private function writeMeasureHeaders(Song $song): void
    {
        $timeSignature = new TimeSignature();
        if ($song->countMeasureHeaders()) {
            foreach ($song->getMeasureHeaders() as $header) {
                $this->writeMeasureHeader($header, $timeSignature);
                $timeSignature->setNumerator($header->getTimeSignature()->getNumerator());
                $timeSignature->getDenominator()->setValue(
                    $header->getTimeSignature()->getDenominator()->getValue()
                );
            }
        }
    }

    private function writeMeasures(Song $song, Tempo $tempo): void
    {
        foreach ($song->getMeasureHeaders() as $index => $header) {
            foreach ($song->getTracks() as $track) {
                $measure = $track->getMeasure($index);
                $this->writeMeasure(
                    $measure,
                    $header->getTempo()->getValue() !== $tempo->getValue()
                );
            }

            $tempo->copyFrom($header->getTempo());
        }
    }

    private function writeMixChange(Tempo $tempo): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->writeByte(-1);
        }

        $this->writeInt($tempo->getValue());
        $this->writeByte(0);
    }

    private function writeNote(Note $note): void
    {
        $flags = 0x20 | 0x10;
        $effect = $note->getEffect();

        if ($effect->isGhostNote()) {
            $flags |= 0x04;
        }

        if ($effect->isBend()
            || $effect->isGrace()
            || $effect->isSlide()
            || $effect->isHammer()
            || $effect->isLetRing()
        ) {
            $flags |= 0x08;
        }

        $this->writeUnsignedByte($flags);

        if (($flags & 0x20) !== 0) {
            $typeHeader = 0x01;
            if ($note->isTiedNote()) {
                $typeHeader = 0x02;
            } elseif ($effect->isDeadNote()) {
                $typeHeader = 0x03;
            }

            $this->writeUnsignedByte($typeHeader);
        }

        if (($flags & 0x10) !== 0) {
            $this->writeByte(intval((($note->getVelocity() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1));
        }

        if (($flags & 0x20) !== 0) {
            $this->writeByte($note->getValue());
        }

        if (($flags & 0x08) !== 0) {
            $this->writeNoteEffects($effect);
        }
    }

    private function writeNoteEffects(NoteEffect $effect): void
    {
        $flags = 0;
        if ($effect->isBend()) {
            $flags |= 0x01;
        }

        if ($effect->isHammer()) {
            $flags |= 0x02;
        }

        if ($effect->isSlide()) {
            $flags |= 0x04;
        }

        if ($effect->isLetRing()) {
            $flags |= 0x08;
        }

        if ($effect->isGrace()) {
            $flags |= 0x10;
        }

        $this->writeUnsignedByte($flags);

        if (($flags & 0x01) !== 0) {
            $this->writeBend($effect->getBend());
        }

        if (($flags & 0x10) !== 0) {
            $this->writeGrace($effect->getGrace());
        }
    }

    /**
     * @return array<string>
     */
    private function toCommentLines(string $comments): array
    {
        $lines = [];

        while (strlen($comments) > 127) {
            $subline = substr($comments, 0, 127);
            $lines[] = $subline;
            $comments = substr($comments, 127);
        }

        $lines[] = $comments;

        return $lines;
    }

    private function writeText(Text $text): void
    {
        $this->writeStringByteSizeOfInteger($text->getValue());
    }

    private function writeTrack(Track $track): void
    {
        $channel = $this->getChannelRoute($track->getChannelId());

        $flags = 0;
        if ($track->getSong()->getChannelById($track->getChannelId())->isPercussionChannel()) {
            $flags |= 0x01;
        }

        $this->writeUnsignedByte($flags);

        $this->writeStringByte($track->getName(), 40);
        $this->writeInt($track->countStrings());
        for ($i = 0; $i < 7; $i++) {
            $value = 0;
            if ($track->countStrings() > $i) {
                $value = $track->getString($i + 1)->getValue();
            }
            $this->writeInt($value);
        }

        $this->writeInt(1);
        $this->writeInt($channel->getChannel1() + 1);
        $this->writeInt($channel->getChannel2() + 1);
        $this->writeInt(24);
        $this->writeInt(min(max($track->getOffset(), 0), 12));
        $this->writeColor($track->getColor());
    }

    private function writeTracks(Song $song): void
    {
        foreach ($song->getTracks() as $track) {
            $this->writeTrack($track);
        }
    }
}
