<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\Midi;

class MidiChannel
{
    private $channel;
    private $instrument;
    private $volume;
    private $balance;
    private $track;

    public function __construct(int $channel)
    {
        $this->channel = $channel;
        $this->instrument = 0;
        $this->volume = 127;
        $this->balance = 64;
        $this->track = -1;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }

    public function getChannel(): int
    {
        return $this->channel;
    }

    public function getInstrument(): int
    {
        return $this->instrument;
    }

    public function setInstrument(int $instrument): void
    {
        $this->instrument = $instrument;
    }

    public function getTrack(): int
    {
        return $this->track;
    }

    public function setTrack(int $track): void
    {
        $this->track = $track;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): void
    {
        $this->volume = $volume;
    }
}
