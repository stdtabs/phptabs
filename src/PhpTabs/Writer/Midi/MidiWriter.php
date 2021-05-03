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

use Exception;
use PhpTabs\Music\Song;
use PhpTabs\Reader\Midi\MidiEvent;
use PhpTabs\Reader\Midi\MidiMessage;
use PhpTabs\Reader\Midi\MidiReader;
use PhpTabs\Reader\Midi\MidiReaderInterface;
use PhpTabs\Reader\Midi\MidiSequence;
use PhpTabs\Reader\Midi\MidiSettings;
use PhpTabs\Reader\Midi\MidiTrack;
use PhpTabs\Share\ChannelRouter;
use PhpTabs\Share\ChannelRouterConfigurator;

final class MidiWriter extends MidiWriterBase
{
    public const ADD_DEFAULT_CONTROLS = 0x01;
    public const ADD_MIXER_MESSAGES = 0x02;
    public const ADD_METRONOME = 0x04;
    public const ADD_FIRST_TICK_MOVE = 0x08;
    public const BANK_SELECT = 0x00;
    public const VOLUME = 0x07;
    public const BALANCE = 0x0A;
    public const EXPRESSION = 0x0B;
    public const REVERB = 0x5B;
    public const TREMOLO = 0x5C;
    public const CHORUS = 0x5D;
    public const PHASER = 0x5F;
    public const DATA_ENTRY_MSB = 0x06;
    public const DATA_ENTRY_LSB = 0x26;
    public const RPN_LSB = 0x64;
    public const RPN_MSB = 0x65;
    public const ALL_NOTES_OFF = 0x7B;

    public function __construct(Song $song)
    {
        if ($song->isEmpty()) {
            throw new Exception('Song is empty');
        }

        // Build sequence
        $channelRouter = new ChannelRouter();
        $channelRouterConfigurator = new ChannelRouterConfigurator($channelRouter);
        $channelRouterConfigurator->configureRouter($song->getChannels());
        $settings = (new MidiSettings())->getDefaults();

        $midiSequenceParser = new MidiSequenceParser(
            $song,
            (self::ADD_FIRST_TICK_MOVE | self::ADD_DEFAULT_CONTROLS | self::ADD_MIXER_MESSAGES)
        );
        $midiSequenceParser->setTranspose($settings->getTranspose());
        $midiSequenceParser->parse(new MidiSequenceHandler($song->countTracks() + 1, $channelRouter, $this));
    }

    /**
     * Starts write process
     */
    public function write(MidiSequence $sequence, int $type): void
    {
        $this->writeInt(MidiReaderInterface::HEADER_MAGIC);
        $this->writeInt(MidiReaderInterface::HEADER_LENGTH);
        $this->writeShort($type);

        // Write tracks
        $this->writeShort($sequence->countTracks());
        $this->writeShort(
            $sequence->getDivisionType() === MidiSequence::PPQ
            ? ($sequence->getResolution() & 0x7fff) : 0
        );

        $countTracks = $sequence->countTracks();
        for ($i = 0; $i < $countTracks; $i++) {
            $this->writeTrack($sequence->getTrack($i), 'ok');
        }
    }

    /**
     * Write a track
     */
    private function writeTrack(MidiTrack $track, ?string $out): int
    {
        $length = 0;
        if ($out !== null) {
            $this->writeInt(MidiReader::TRACK_MAGIC);
        }

        if ($out !== null) {
            $this->writeInt($this->writeTrack($track, null));
        }

        $previous = null;

        // Bon jusqu'à l'écriture du 1er événement
        $countEvents = $track->countEvents();
        for ($i = 0; $i < $countEvents; $i++) {
            $event = $track->get($i);
            $length += $this->writeEvent($event, $previous, $out);
            $previous = $event;
        }

        return $length;
    }

    /**
     * Write a MIDI event
     */
    private function writeEvent(MidiEvent $event, ?MidiEvent $previous = null, ?string $out = null): int
    {
        // @todo Remove this lines when timing will be patched
        // time should not be < 0
        $time = $previous !== null
            ? $event->getTick() - $previous->getTick()
            : 0;
        if ($time < 0) {
            $time = abs($event->getTick() - $previous->getTick());
        }

        $length = $this->writeVariableLengthQuantity(
            $time,
            $out
        );

        $message = $event->getMessage();

        if ($message->getType() === MidiMessage::TYPE_SHORT) {
            $length += $this->writeShortMessage($message, $out);
        } elseif ($message->getType() === MidiMessage::TYPE_META) {
            $length += $this->writeMetaMessage($message, $out);
        }

        return $length;
    }

    /**
     * Writes a short MIDI message
     */
    private function writeShortMessage(MidiMessage $message, ?string $out): int
    {
        $data = $message->getData();

        $length = count($data);
        if ($out !== null) {
            // Attention aux paramètres de write en java
            $this->writeUnsignedBytes($message->getData());
        }
        return $length;
    }

    /**
     * Writes a meta MIDI message
     */
    protected function writeMetaMessage(MidiMessage $message, ?string $out): int
    {
        $length = 0;
        $data = $message->getData();

        if ($out !== null) {
            $this->writeUnsignedBytes([0xFF]);
            $this->writeUnsignedBytes([$message->getCommand()]);
        }

        $length += 2;
        $length += $this->writeVariableLengthQuantity(count($data), $out);

        if ($out !== null) {
            $this->writeUnsignedBytes($data);
        }

        return $length + count($data);
    }
}
