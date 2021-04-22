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

use PhpTabs\Music\TimeSignature;
use PhpTabs\Reader\Midi\MidiMessage;

class MidiMessageUtils
{
    const TICK_MOVE = 0x01;

    private static function fixValue(int $value): int
    {
        $fixedValue = $value;
        $fixedValue = min($fixedValue, 127);

        return max($fixedValue, 0);
    }

    private static function fixChannel(int $channel): int
    {
        $fixedChannel = $channel;
        $fixedChannel = min($fixedChannel, 15);

        return max($fixedChannel, 0);
    }

    public static function noteOn(int $channel, int $note, int $velocity): MidiMessage
    {
        return MidiMessage::shortMessage(MidiMessage::NOTE_ON, self::fixChannel($channel), self::fixValue($note), self::fixValue($velocity));
    }

    public static function noteOff(int $channel, int $note, int $velocity): MidiMessage
    {
        return MidiMessage::shortMessage(MidiMessage::NOTE_OFF, self::fixChannel($channel), self::fixValue($note), self::fixValue($velocity));
    }

    public static function controlChange(int $channel, int $controller, int $value): MidiMessage
    {
        return MidiMessage::shortMessage(MidiMessage::CONTROL_CHANGE, self::fixChannel($channel), self::fixValue($controller), self::fixValue($value));
    }

    public static function programChange(int $channel, int $instrument): MidiMessage
    {
        return MidiMessage::shortMessage(MidiMessage::PROGRAM_CHANGE, self::fixChannel($channel), self::fixValue($instrument));
    }

    public static function pitchBend(int $channel, int $value): MidiMessage
    {
        return MidiMessage::shortMessage(MidiMessage::PITCH_BEND, self::fixChannel($channel), 0, self::fixValue($value));
    }

    public static function systemReset(): MidiMessage
    {
        return MidiMessage::shortMessage(MidiMessage::SYSTEM_RESET);
    }

    public static function tempoInUSQ(int $usq): MidiMessage
    {
        $message = new MidiMessage(MidiMessage::TYPE_META, MidiMessage::TEMPO_CHANGE);
        $message->setData([
            (($usq >> 16) & 0xff),
            (($usq >> 8) & 0xff),
            (($usq) & 0xff)
        ]);
        return $message;
    }

    public static function timeSignature(TimeSignature $timeSignature): MidiMessage
    {
        $message = new MidiMessage(MidiMessage::TYPE_META, MidiMessage::TIME_SIGNATURE_CHANGE);
        $message->setData([
            $timeSignature->getNumerator(),
            $timeSignature->getDenominator()->getIndex(),
            (96 / $timeSignature->getDenominator()->getValue()),
            8
        ]);
        return $message;
    }

    public static function endOfTrack(): MidiMessage
    {
        return MidiMessage::metaMessage(47, []);
    }
}
