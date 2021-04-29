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

final class MidiChannel
{
    /**
     * @var int
     */
    private $channel;

    /**
     * @var int
     */
    private $instrument = 0;

    /**
     * @var int
     */
    private $volume = 127;

    /**
     * @var int
     */
    private $balance = 64;

    /**
     * @var int
     */
    private $track = -1;

    public function __construct(int $channel)
    {
        $this->channel = $channel;
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
