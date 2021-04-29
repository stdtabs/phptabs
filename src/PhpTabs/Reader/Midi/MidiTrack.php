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

/**
 * Midi track
 */
final class MidiTrack
{
    /**
     * @var int
     */
    private $ticks = 0;

    /**
     * @var array<MidiEvent>
     */
    private $events = [];

    public function add(MidiEvent $event): void
    {
        $this->events[] = $event;
        $this->ticks = max([$this->ticks, $event->getTick()]);
    }

    public function get(int $index): MidiEvent
    {
        return $this->events[$index];
    }

    public function countEvents(): int
    {
        return count($this->events);
    }

    public function ticks(): int
    {
        return $this->ticks;
    }
}
