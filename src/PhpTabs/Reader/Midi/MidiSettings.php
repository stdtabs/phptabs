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
