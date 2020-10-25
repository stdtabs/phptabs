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

class MidiSettings
{
    const VOLUME = 0x07;
    const BALANCE = 0x0A;
    const EXPRESSION = 0x0B;
    const REVERB = 0x5B;
    const TREMOLO = 0x5C;
    const CHORUS = 0x5D;
    const PHASER = 0x5F;
    const DATA_ENTRY_MSB = 0x06;
    const DATA_ENTRY_LSB = 0x26;
    const RPN_LSB = 0x64;
    const RPN_MSB = 0x65;
    const ALL_NOTES_OFF = 0x7B;

    private $transpose;

    public function __construct()
    {
        $this->transpose = 0;
    }

    public function getTranspose(): int
    {
        return $this->transpose;
    }

    public function setTranspose(int $transpose): void
    {
        $this->transpose = $transpose;
    }

    public static function getDefaults(): MidiSettings
    {
        return new MidiSettings();
    }
}
