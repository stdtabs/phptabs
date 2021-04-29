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
 * Midi event
 */
final class MidiEvent
{
    /**
     * @var int
     */
    private $tick;

    /**
     * @var MidiMessage
     */
    private $message;

    public function __construct(MidiMessage $message, int $tick)
    {
        $this->message = $message;
        $this->tick = $tick;
    }

    public function getMessage(): MidiMessage
    {
        return $this->message;
    }

    public function getTick(): int
    {
        return $this->tick;
    }
}
