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

final class MidiNote
{
    /**
     * @var int
     */
    private $track;

    /**
     * @var int
     */
    private $channel;

    /**
     * @var int
     */
    private $tick;

    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $velocity;

    /**
     * @var array<int>
     */
    private $pitchBends = [];

    public function __construct(int $track, int $channel, int $tick, int $value, int $velocity)
    {
        $this->track = $track;
        $this->channel = $channel;
        $this->tick = $tick;
        $this->value = $value;
        $this->velocity = $velocity;
    }

    public function getChannel(): int
    {
        return $this->channel;
    }

    public function getTick(): int
    {
        return $this->tick;
    }

    public function getTrack(): int
    {
        return $this->track;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getVelocity(): int
    {
        return $this->velocity;
    }

    public function addPitchBend(int $value): void
    {
        $this->pitchBends[] = $value;
    }

    /**
     * @return array<int>
     */
    public function getPitchBends(): array
    {
        return $this->pitchBends;
    }

    public function countPitchBends(): int
    {
        return count($this->pitchBends);
    }
}
