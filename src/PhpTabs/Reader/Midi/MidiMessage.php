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

class MidiMessage
{
    const TYPE_SHORT = 1;
    const TYPE_META = 2;

    const NOTE_OFF = 0x80;
    const NOTE_ON = 0x90;
    const CONTROL_CHANGE = 0xB0;
    const PROGRAM_CHANGE = 0xC0;
    const PITCH_BEND = 0xE0;
    const SYSTEM_RESET = 0xFF;
    const TEMPO_CHANGE = 0x51;
    const TIME_SIGNATURE_CHANGE = 0x58;

    private $message;
    private $command;
    private $data;

    public function __construct(int $message, int $command)
    {
        $this->message = $message;
        $this->command = $command;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getType(): int
    {
        return $this->message;
    }

    public function getCommand(): int
    {
        return $this->command;
    }

    public static function shortMessage(int $command, int $channel = null, int $data1 = null, int $data2 = null): MidiMessage
    {
        $message = new MidiMessage(self::TYPE_SHORT, $command);

        if ($channel === null && $data1 === null && $data2 === null) {
            $message->setData([$command]);
        } elseif ($data2 === null) {
            $message->setData([
                ($command & 0xF0) | ($channel & 0x0F),
                $data1
            ]);
        } else {
            $message->setData([
                ($command & 0xF0) | ($channel & 0x0F),
                $data1,
                $data2
            ]);
        }

        return $message;
    }

    public static function metaMessage(int $command, array $data): MidiMessage
    {
        $message = new MidiMessage(self::TYPE_META, $command);
        $message->setData($data);

        return $message;
    }
}
