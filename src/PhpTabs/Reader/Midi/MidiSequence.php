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

class MidiSequence
{
    /**
     * Sequence
     */
    public const PPQ = 0.0;
    public const SMPTE_24 = 24.0;
    public const SMPTE_25 = 25.0;
    public const SMPTE_30DROP = 29.97;
    public const SMPTE_30 = 30.0;

    protected $divisionType;
    protected $resolution;
    private $tracks;

    public function __construct(float $divisionType, int $resolution)
    {
        $this->divisionType = $divisionType;
        $this->resolution = $resolution;
        $this->tracks = [];
    }

    public function addTrack(MidiTrack $track): void
    {
        $this->tracks[] = $track;
    }

    public function getTrack(int $index): ?MidiTrack
    {
        return $this->tracks[$index] ?? null;
    }

    /**
     * Counts MIDI tracks
     */
    public function countTracks(): int
    {
        return count($this->tracks);
    }

    public function getDivisionType(): float
    {
        return $this->divisionType;
    }

    public function getResolution(): int
    {
        return $this->resolution;
    }

    public function finish(): void
    {
        for ($i = 0; $i < count($this->tracks); $i++) {
            $track = $this->tracks[$i];

            $track->add(new MidiEvent(MidiMessage::metaMessage(47, [1]), $track->ticks()));
        }
    }
}
