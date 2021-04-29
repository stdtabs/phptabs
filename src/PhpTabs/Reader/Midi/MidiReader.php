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

namespace PhpTabs\Reader\Midi;

use Exception;
use PhpTabs\Component\InputStream;
use PhpTabs\Component\Log;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\Channel;
use PhpTabs\Music\Color;
use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Note;
use PhpTabs\Music\Song;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\Share\ChannelRoute;
use PhpTabs\Share\ChannelRouter;

final class MidiReader extends MidiReaderBase
{
    public const CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX = true;
    public const STATUS_NONE = 0;
    public const STATUS_ONE_BYTE = 1;
    public const STATUS_TWO_BYTES = 2;
    public const STATUS_SYSEX = 3;
    public const STATUS_META = 4;

    /**
     * @var int
     */
    private $resolution;

    /**
     * @var array<Channel>
     */
    private $channels;

    /**
     * @var array<MeasureHeader>
     */
    private $headers;

    /**
     * @var array<Track>
     */
    private $tracks;

    /**
     * @var array<MidiNote>
     */
    private $tempNotes;

    /**
     * @var array<MidiChannel>
     */
    private $tempChannels;

    /**
     * @var array<MidiTrackTuningHelper>
     */
    private $trackTuningHelpers;

    /**
     * @var MidiSettings
     */
    private $settings;

    /**
     * @param \PhpTabs\Component\InputStream $file An input file to read
     */
    public function __construct(InputStream $file)
    {
        parent::__construct($file);

        $song = new Song();

        $this->setTablature($song);

        $this->settings = (new MidiSettings())->getDefaults();
        $this->sequence = $this->getSequence();
        $this->initFields($this->sequence);

        $countTracks = $this->sequence->countTracks();

        for ($i = 0; $i < $countTracks; $i++) {

            $track = $this->sequence->getTrack($i);
            $trackNumber = $this->getNextTrackNumber();
            $events = $track->countEvents();

            for ($j = 0; $j < $events; $j++) {
                $event = $track->get($j);

                $this->parseMessage($trackNumber, $event->getTick(), $event->getMessage());
            }
        }

        $this->checkAll();

        array_walk(
            $this->channels,
            static function ($channel) use (&$song): void {
                $song->addChannel($channel);
            }
        );

        array_walk(
            $this->headers,
            static function ($header) use (&$song): void {
                $song->addMeasureHeader($header);
            }
        );

        array_walk(
            $this->tracks,
            static function ($track) use (&$song): void {
                $song->addTrack($track);
            }
        );

        $this->adjust($song);

        $this->closeStream();
    }

    public function getTablature(): Tablature
    {
        return $this->tablature ?? new Tablature();
    }

    /**
     * Initialize Tablature with read Song
     */
    private function setTablature(Song $song): void
    {
        if (! isset($this->tablature)) {
            $this->tablature = new Tablature();
        }

        $this->tablature->setSong($song);
        $this->tablature->setFormat('mid');
    }

    /*-------------------------------------------------------------------
    * Private methods are below
    * -----------------------------------------------------------------*/

    private function adjust(Song $song): void
    {
        (new MidiAdjuster($song))->adjustSong();
    }

    /**
     * @throws \Exception if measure headers or track are not defined
     */
    private function checkAll(): void
    {
        $this->checkChannels();
        $this->checkTracks();

        $headerCount = count($this->headers);
        $trackCount = count($this->tracks);

        for ($i = 0; $i < $trackCount; $i++) {

            $track = $this->tracks[$i];

            while ($track->countMeasures() < $headerCount) {

                $start = Duration::QUARTER_TIME;

                $lastMeasure = $track->countMeasures() > 0
                    ? $track->getMeasure($track->countMeasures() - 1)
                    : null;

                if ($lastMeasure !== null) {
                    $start = $lastMeasure->getStart() + $lastMeasure->getLength();
                }

                $track->addMeasure(new Measure($this->getHeader($start)));
            }
        }

        if (! count($this->headers) || ! count($this->tracks)) {
            throw new Exception('Empty Song');
        }
    }

    private function checkChannels(): void
    {
        $countTempChannels = count($this->tempChannels);
        for ($tc = 0; $tc < $countTempChannels; $tc++) {
            $tempChannel = $this->tempChannels[$tc];

            if ($tempChannel->getTrack() > 0) {
                $channelExists = false;

                $countChannels = count($this->channels);
                for ($c = 0; $c < $countChannels; $c++) {
                    $channel = $this->channels[$c];
                    $channelRoute = $this->channelRouter->getRoute($channel->getId());
                    if ($channelRoute !== null) {
                        if ($channelRoute->getChannel1() === $tempChannel->getChannel()
                            || $channelRoute->getChannel2() === $tempChannel->getChannel()
                        ) {
                              $channelExists = true;
                        }
                    }
                }

                if (! $channelExists) {
                    $channel = new Channel();
                    $channel->setId(count($this->channels) + 1);
                    $channel->setProgram($tempChannel->getInstrument());
                    $channel->setVolume($tempChannel->getVolume());
                    $channel->setBalance($tempChannel->getBalance());
                    $channel->setName('#' . $channel->getId());
                    $channel->setBank(
                        $tempChannel->getChannel() === 9
                            ? Channel::DEFAULT_PERCUSSION_BANK
                            : Channel::DEFAULT_BANK
                    );

                    $channelRoute = new ChannelRoute($channel->getId());
                    $channelRoute->setChannel1($tempChannel->getChannel());
                    $channelRoute->setChannel2($tempChannel->getChannel());

                    $count = count($this->tempChannels);
                    for ($tcAux = $tc + 1; $tcAux < $count; $tcAux++) {
                        $tempChannelAux = $this->tempChannels[$tcAux];

                        if ($tempChannel->getTrack() === $tempChannelAux->getTrack()) {
                            if ($channelRoute->getChannel2() === $channelRoute->getChannel1()) {
                                $channelRoute->setChannel2($tempChannelAux->getChannel());
                            } else {
                                $tempChannelAux->setTrack(-1);
                            }
                        }
                    }

                    $this->channelRouter->configureRoutes($channelRoute, ($tempChannel->getChannel() === 9));
                    $this->channels[] = $channel;
                }
            }
        }
    }

    private function checkTracks(): void
    {
        array_walk(
            $this->tracks, function ($track): void {
                $trackChannel = null;

                array_walk(
                    $this->tempChannels, function ($tempChannel) use (&$trackChannel, $track): void {
                        if ($tempChannel->getTrack() === $track->getNumber()) {
                            array_walk(
                                $this->channels, function ($channel) use (&$tempChannel, &$trackChannel): void {
                                    $channelRoute = $this->channelRouter->getRoute($channel->getId());

                                    if ($channelRoute !== null && $tempChannel->getChannel() === $channelRoute->getChannel1()) {
                                        $trackChannel = $channel;
                                    }
                                }
                            );
                        }
                    }
                );

                if ($trackChannel !== null) {
                    $track->setChannelId($trackChannel->getId());
                }

                if ($trackChannel !== null && $trackChannel->isPercussionChannel()) {
                    $track->setStrings($this->createPercussionStrings(6));
                } else {
                    $track->setStrings($this->getTrackTuningHelper($track->getNumber())->getStrings());
                }
            }
        );
    }

    /**
     * Create percussion strings
     *
     * @return array<TabString>
     */
    private function createPercussionStrings(int $stringCount): array
    {
        $strings = [];

        for ($i = 1; $i <= $stringCount; $i++) {
            $strings[] = new TabString($i, 0);
        }

        return $strings;
    }

    private function getHeader(int $tick): MeasureHeader
    {
        $realTick = $tick >= Duration::QUARTER_TIME
                  ? $tick
                  : Duration::QUARTER_TIME;

        foreach ($this->headers as $header) {
            if ($realTick >= $header->getStart()
                && $realTick < $header->getStart() + $header->getLength()
            ) {
                return $header;
            }
        }

        $last = $this->getLastHeader();
        $header = new MeasureHeader();

        $header->setNumber(
            $last !== null
            ? $last->getNumber() + 1 : 1
        );

        $header->setStart(
            $last !== null
            ? $last->getStart() + $last->getLength()
            : Duration::QUARTER_TIME
        );

        $header->getTempo()->setValue(
            $last !== null
            ? $last->getTempo()->getValue() : 120
        );

        if ($last !== null) {
            $header->getTimeSignature()->copyFrom($last->getTimeSignature());
        } else {
            $header->getTimeSignature()->setNumerator(4);
            $header->getTimeSignature()->getDenominator()->setValue(Duration::QUARTER);
        }

        $this->headers[] = $header;

        if ($realTick >= $header->getStart()
            && $realTick < $header->getStart() + $header->getLength()
        ) {
            return $header;
        }

        return $this->getHeader($realTick);
    }

    private function getLastHeader(): ?MeasureHeader
    {
        return count($this->headers)
            ? $this->headers[count($this->headers) - 1]
            : null;
    }

    private function getMeasure(Track $track, int $tick): Measure
    {
        $realTick = $tick >= Duration::QUARTER_TIME
        ? $tick : Duration::QUARTER_TIME;

        $measures = $track->getMeasures();

        foreach ($measures as $measure) {
            if ($realTick >= $measure->getStart() && $realTick < $measure->getStart() + $measure->getLength()) {
                return $measure;
            }
        }

        $this->getHeader($realTick);

        $countHeaders = count($this->headers);
        for ($i = 0; $i < $countHeaders; $i++) {
            $exist = false;
            $header = $this->headers[$i];
            $measureCount = $track->countMeasures();

            for ($j = 0; $j < $measureCount; $j++) {
                $measure = $track->getMeasure($j);

                if ($measure->getHeader() === $header) {
                    $exist = true;
                }
            }

            if (! $exist) {
                $measure = new Measure($header);
                $track->addMeasure($measure);
            }
        }

        return $this->getMeasure($track, $realTick);
    }

    private function getNextTrackNumber(): int
    {
        return count($this->tracks) + 1;
    }

    public function getTempChannel(int $channel): MidiChannel
    {
        foreach ($this->tempChannels as $tempChannel) {
            if ($tempChannel->getChannel() === $channel) {
                return $tempChannel;
            }
        }

        $tempChannel = new MidiChannel($channel);
        $this->tempChannels[] = $tempChannel;

        return $tempChannel;
    }

    private function getTempNote(int $track, int $channel, int $value, bool $purge): ?MidiNote
    {
        $countTempNotes = count($this->tempNotes);

        for ($i = 0; $i < $countTempNotes; $i++) {
            $note = $this->tempNotes[$i];

            if ($note->getTrack() === $track
                && $note->getChannel() === $channel
                && $note->getValue() === $value
            ) {
                if ($purge) {
                    array_splice($this->tempNotes, $i, 1);
                }

                return $note;
            }
        }

        return null;
    }

    private function getTrackTuningHelper(int $track): MidiTrackTuningHelper
    {
        foreach ($this->trackTuningHelpers as $helper) {
            if ($helper->getTrack() === $track) {
                return $helper;
            }
        }

        $helper = new MidiTrackTuningHelper($track);
        $this->trackTuningHelpers[] = $helper;

        return $helper;
    }

    private function parseMessage(int $trackNumber, int $tick, MidiMessage $message): void
    {
        $parsedTick = $this->parseTick($tick + $this->resolution);

        //NOTE ON
        if ($message->getType() === MidiMessage::TYPE_SHORT && $message->getCommand() === MidiMessage::NOTE_ON) {
            $this->parseNoteOn($trackNumber, $parsedTick, $message->getData());
        }
        //NOTE OFF
        elseif ($message->getType() === MidiMessage::TYPE_SHORT && $message->getCommand() === MidiMessage::NOTE_OFF) {
            $this->parseNoteOff($trackNumber, $parsedTick, $message->getData());
        }
        //PROGRAM CHANGE
        elseif ($message->getType() === MidiMessage::TYPE_SHORT && $message->getCommand() === MidiMessage::PROGRAM_CHANGE) {
            $this->parseProgramChange($message->getData());
        }
        //CONTROL CHANGE
        elseif ($message->getType() === MidiMessage::TYPE_SHORT && $message->getCommand() === MidiMessage::CONTROL_CHANGE) {
            $this->parseControlChange($message->getData());
        }
        //PITCH BEND
        elseif ($message->getType() === MidiMessage::TYPE_SHORT && $message->getCommand() === MidiMessage::PITCH_BEND) {
            $this->parsePitchBend($message->getData());
        }
        //TIME SIGNATURE
        elseif ($message->getType() === MidiMessage::TYPE_META && $message->getCommand() === MidiMessage::TIME_SIGNATURE_CHANGE) {
            $this->parseTimeSignature($parsedTick, $message->getData());
        }
        //TEMPO
        elseif ($message->getType() === MidiMessage::TYPE_META && $message->getCommand() === MidiMessage::TEMPO_CHANGE) {
            $this->parseTempo($parsedTick, $message->getData());
        }
        // SKIPPED MESSAGES (Undefined commands)
        else {
            $logMessage = sprintf(
                'track=%d, tick=%d, type=%s, command=%x, data=array(%s)',
                $trackNumber,
                $tick,
                $message->getType(),
                $message->getCommand(),
                implode(', ', $message->getData())
            );

            Log::add($logMessage, 'MIDI_SKIPPED_MESSAGE');
        }
    }

    private function getSequence(): MidiSequence
    {
        if ($this->readInt() !== MidiReaderInterface::HEADER_MAGIC) {
            throw new Exception('Not a MIDI file: wrong header magic');
        }

        $headerLength = $this->readInt();

        if ($headerLength < MidiReaderInterface::HEADER_LENGTH) {
            throw new Exception('Corrupted MIDI file: wrong header length');
        }

        $type = $this->readShort();

        if ($type < 0 || $type > 2) {
            throw new Exception('Corrupted MIDI file: illegal type');
        }

        if ($type === 2) {
            throw new Exception('this implementation doesn\'t support type 2 MIDI files');
        }

        $trackCount = $this->readShort();

        if ($trackCount <= 0) {
            throw new Exception('Corrupted MIDI file: number of tracks must be positive');
        }

        if ($type === 0 && $trackCount !== 1) {
            throw new Exception("Corrupted MIDI file:  type 0 files must contain exactly one track {$trackCount}");
        }

        $divisionType = -1.0;
        $resolution = -1;
        $division = $this->readUnsignedShort();

        if (($division & 0x8000) !== 0) {
            $frameType = -(($division >> 8) & 0xff);

            switch ($frameType) {
            case 24:
                $divisionType = MidiReaderInterface::SMPTE_24;
                break;
            case 25:
                $divisionType = MidiReaderInterface::SMPTE_25;
                break;
            case 29:
                $divisionType = MidiReaderInterface::SMPTE_30DROP;
                break;
            case 30:
                $divisionType = MidiReaderInterface::SMPTE_30;
                break;
            default:
                throw new Exception('Corrupted MIDI file: illegal frame division type');
            }

            $resolution = $division & 0xff;
        } else {
            $divisionType = MidiReaderInterface::PPQ;
            $resolution = $division & 0x7fff;
        }

        $this->skip($headerLength - MidiReaderInterface::HEADER_LENGTH);

        $sequence = new MidiSequence($divisionType, $resolution);

        for ($i = 0; $i < $trackCount; $i++) {
            $track = new MidiTrack();
            $sequence->addTrack($track);
            $this->readTrack($track);
        }

        return $sequence;
    }

    private function getTrack(int $number): Track
    {
        foreach ($this->tracks as $track) {
            if ($track->getNumber() === $number) {
                return $track;
            }
        }

        $track = new Track();
        $track->setNumber($number);
        $track->setChannelId(-1);
        $track->getColor()->setR(Color::RED[0]);
        $track->getColor()->setG(Color::RED[1]);
        $track->getColor()->setB(Color::RED[2]);

        $this->tracks[] = $track;

        return $track;
    }

    private function getType(int $statusByte): int
    {
        if ($statusByte < 0xf0) {
            $command = $statusByte & 0xf0;

            switch ($command) {
                case 0x80:
                case 0x90:
                case 0xa0:
                case 0xb0:
                case 0xe0:
                    return MidiReader::STATUS_TWO_BYTES;
                case 0xc0:
                case 0xd0:
                    return MidiReader::STATUS_ONE_BYTE;
            }
        }

        switch ($statusByte) {
            case 0xf0:
            case 0xf7:
                return MidiReader::STATUS_SYSEX;
            case 0xff:
                return MidiReader::STATUS_META;
        }

        return MidiReader::STATUS_NONE;
    }

    private function initFields(MidiSequence $sequence): void
    {
        $this->resolution = $sequence->getResolution();
        $this->channels = [];
        $this->headers = [];
        $this->tracks = [];
        $this->tempNotes = [];
        $this->tempChannels = [];
        $this->trackTuningHelpers = [];
        $this->channelRouter = new ChannelRouter();
    }

    private function makeNote(int $tick, int $track, int $channel, int $value): void
    {
        $tempNote = $this->getTempNote($track, $channel, $value, true);

        if ($tempNote !== null) {
            $nString = 0;
            $nValue = $tempNote->getValue() + $this->settings->getTranspose();
            $nVelocity = $tempNote->getVelocity();
            $nStart = $tempNote->getTick();

            $minDuration = new Duration();
            $minDuration->setValue(Duration::SIXTY_FOURTH);
            $nDuration = Duration::fromTime($tick - $tempNote->getTick(), $minDuration);

            $measure = $this->getMeasure($this->getTrack($track), $tempNote->getTick());
            $beat = $measure->getBeatByStart($nStart);
            $beat->getVoice(0)->getDuration()->copyFrom($nDuration);

            $note = new Note();
            $note->setValue($nValue);
            $note->setString($nString);
            $note->setVelocity($nVelocity);

            // Effect Bends / Vibrato
            if ($tempNote->countPitchBends() > 0) {
                $this->makeNoteEffect($note, $tempNote->getPitchBends());
            }

            $beat->getVoice(0)->addNote($note);
        }
    }

    /**
     * @param array<int> $pitchBends
     */
    private function makeNoteEffect(Note $note, array $pitchBends): void
    {
        $tmp = [];

        foreach ($pitchBends as $pitchBend) {
            if (! in_array($pitchBend, $tmp)) {
                array_push($tmp, $pitchBend);
            }
        }

        // All pitches have the same value: vibrato
        if (count($tmp) === 1) {
            $note->getEffect()->setVibrato(true);

            return;
        }

        // Bend
        $bend = new EffectBend();
        $bend->addPoint(0, 0);

        foreach ($pitchBends as $k => $pitchBend) {
            $bend->addPoint($k, $pitchBend);
        }

        $note->getEffect()->setBend($bend);
    }

    private function makeTempNotesBefore(int $tick, int $track): void
    {
        $nextTick = $tick;

        $countTempNotes = count($this->tempNotes);

        for ($i = 0; $i < $countTempNotes; $i++) {
            $note = $this->tempNotes[$i];

            if ($note->getTick() < $nextTick && $note->getTrack() === $track) {
                $nextTick = $note->getTick() + (Duration::QUARTER_TIME * 5); //First beat + 4/4 measure;
                $this->makeNote($nextTick, $track, $note->getChannel(), $note->getValue());
                break;
            }
        }
    }

    /**
     * @param array<int> $data
     */
    private function parseControlChange(array $data): void
    {
        $length = count($data);
        $channel = $length > 0 ? (($data[0] & 0xff) & 0x0f) : -1;
        $control = $length > 1 ? ($data[1] & 0xff) : -1;
        $value = $length > 2 ? ($data[2] & 0xff) : -1;

        if ($channel !== -1 && $control !== -1 && $value !== -1) {
            if ($control === MidiSettings::VOLUME) {
                $this->getTempChannel($channel)->setVolume($value);
            } elseif ($control === MidiSettings::BALANCE) {
                $this->getTempChannel($channel)->setBalance($value);
            }
        }
    }

    /**
     * @param array<int> $data
     */
    private function parseNoteOff(int $track, int $tick, array $data): void
    {
        $length = count($data);

        $channel = $length > 0 ? (($data[0] & 0xff) & 0x0f) : 0;
        $value = $length > 1 ? ($data[1] & 0xff) : 0;

        $this->makeNote($tick, $track, $channel, $value);
    }

    /**
     * @param array<int> $data
     */
    private function parseNoteOn(int $track, int $tick, array $data): void
    {
        $length = count($data);
        $channel = $length > 0 ? (($data[0] & 0xff) & 0x0f) : 0;
        $value = $length > 1 ? ($data[1] & 0xff) : 0;
        $velocity = $length > 2 ? ($data[2] & 0xff) : 0;

        if ($velocity === 0) {
            $this->parseNoteOff($track, $tick, $data);
        } elseif ($value > 0) {
            $this->makeTempNotesBefore($tick, $track);
            $this->getTempChannel($channel)->setTrack($track);
            $this->getTrackTuningHelper($track)->checkValue($value);

            $this->tempNotes[] = new MidiNote($track, $channel, $tick, $value, $velocity);
        }
    }

    /**
     * @param array<int> $data
     */
    private function parsePitchBend(array $data): void
    {
        $length = count($data);

        // Resolution
        $value = $length > 2
            ? $data[2] - 0x40
            : 0;

        if ($value !== 0 && count($this->tempNotes) > 0) {
            $noteCount = count($this->tempNotes);

            $this->tempNotes[$noteCount - 1]->addPitchBend($value);
        }
    }

    /**
     * @param array<int> $data
     */
    private function parseProgramChange(array $data): void
    {
        $length = count($data);
        $channel = $length > 0
            ? ($data[0] & 0xff) & 0x0f
            : -1;
        $instrument = $length > 1
            ? $data[1] & 0xff
            : -1;

        if ($channel !== -1 && $instrument !== -1) {
            $this->getTempChannel($channel)->setInstrument($instrument);
        }
    }

    /**
     * @param array<int> $data
     */
    private function parseTempo(int $tick, array $data): void
    {
        if (count($data) >= 3) {
            $tempo = Tempo::fromTPQ(($data[2] & 0xff) | (($data[1] & 0xff) << 8) | (($data[0] & 0xff) << 16));

            $this->getHeader($tick)->setTempo($tempo);
        }
    }

    private function parseTick(int $tick): int
    {
        return intval(abs(Duration::QUARTER_TIME * $tick / $this->resolution));
    }

    /**
     * @param array<int> $data
     */
    private function parseTimeSignature(int $tick, array $data): void
    {
        if (count($data) >= 2) {
            $timeSignature = new TimeSignature();
            $timeSignature->setNumerator($data[0]);
            $timeSignature->getDenominator()->setValue(Duration::QUARTER);

            switch ($data[1]) {
                case 0:
                    $timeSignature->getDenominator()->setValue(Duration::WHOLE);
                    break;
                case 1:
                    $timeSignature->getDenominator()->setValue(Duration::HALF);
                    break;
                case 2:
                    $timeSignature->getDenominator()->setValue(Duration::QUARTER);
                    break;
                case 3:
                    $timeSignature->getDenominator()->setValue(Duration::EIGHTH);
                    break;
                case 4:
                    $timeSignature->getDenominator()->setValue(Duration::SIXTEENTH);
                    break;
                case 5:
                    $timeSignature->getDenominator()->setValue(Duration::THIRTY_SECOND);
                    break;
            }

            $this->getHeader($tick)->setTimeSignature($timeSignature);
        }
    }

    private function readEvent(MidiTrackReaderHelper $helper): ?MidiEvent
    {
        $statusByte = $this->readUnsignedByte();
        $helper->decrementRemainingBytes();
        $savedByte = 0;
        $runningStatusApplies = false;

        if ($statusByte < 0x80) {
            switch ($helper->getRunningStatusByte()) {
                case -1:
                    throw new Exception('Corrupted MIDI file: status byte is missing');
                  break;
                default:
                    $runningStatusApplies = true;
                    $savedByte = $statusByte;
                    $statusByte = $helper->getRunningStatusByte();
                    break;
            }
        }

        $type = $this->getType($statusByte);

        if ($type === MidiReader::STATUS_ONE_BYTE) {
            $data = 0;

            if ($runningStatusApplies) {
                $data = $savedByte;
            } else {
                $data = $this->readUnsignedByte();
                $helper->decrementRemainingBytes();
                $helper->setRunningStatusByte($statusByte);
            }

            return new MidiEvent(MidiMessage::shortMessage(($statusByte & 0xf0), ($statusByte & 0x0f), $data), $helper->getTicks());
        }

        if ($type === MidiReader::STATUS_TWO_BYTES) {
            $data1 = 0;

            if ($runningStatusApplies) {
                $data1 = $savedByte;
            } else {
                $data1 = $this->readUnsignedByte();
                $helper->decrementRemainingBytes();
                $helper->setRunningStatusByte($statusByte);
            }

            $helper->decrementRemainingBytes();

            return new MidiEvent(MidiMessage::shortMessage(($statusByte & 0xf0), ($statusByte & 0x0f), $data1, $this->readUnsignedByte()), $helper->getTicks());
        }

        if ($type === MidiReader::STATUS_SYSEX) {
            $helper->setRunningStatusByte(-1);

            $dataLength = $this->readVariableLengthQuantity($helper);
            $data = [];

            for ($i = 0; $i < $dataLength; $i++) {
                $data[$i] = $this->readUnsignedByte();
                $helper->decrementRemainingBytes();
            }

        } elseif ($type === MidiReader::STATUS_META) {
            $helper->setRunningStatusByte(-1);

            $typeByte = $this->readUnsignedByte();
            $helper->decrementRemainingBytes();
            $dataLength = $this->readVariableLengthQuantity($helper);
            $data = [];

            for ($i = 0; $i < $dataLength; $i++) {
                $data[$i] = $this->readUnsignedByte();
                $helper->decrementRemainingBytes();
            }

            return new MidiEvent(MidiMessage::metaMessage($typeByte, $data), $helper->getTicks());
        }

        return null;
    }

    private function readTrack(MidiTrack $track): void
    {
        while (true) {
            if ($this->readInt() === MidiReaderInterface::TRACK_MAGIC) {
                break;
            }

            $chunkLength = $this->readInt();

            if ($chunkLength % 2 !== 0) {
                $chunkLength++;
            }

            $this->skip($chunkLength);
        }

        $helper = new MidiTrackReaderHelper(0, $this->readInt(), -1);

        while ($helper->getRemainingBytes() > 0) {
            $helper->addTicks($this->readVariableLengthQuantity($helper));

            $event = $this->readEvent($helper);

            if ($event !== null) {
                $track->add($event);
            }
        }
    }
}
