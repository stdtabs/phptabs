<?php

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

  /**
   * @param int $value
   * 
   * @return int
   */
  private static function fixValue($value)
  {
    $fixedValue = $value;
    $fixedValue = min($fixedValue, 127);
    $fixedValue = max($fixedValue, 0);

    return $fixedValue;
  }

  /**
   * @param int $channels
   * 
   * @return int
   */	
  private static function fixChannel($channel)
  {
    $fixedChannel = $channel;
    $fixedChannel = min($fixedChannel, 15);
    $fixedChannel = max($fixedChannel, 0);

    return $fixedChannel;
  }

  /**
   * @param int $channel
   * @param int $note
   * @param int $velocity
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function noteOn($channel, $note, $velocity)
  {
    return MidiMessage::shortMessage(MidiMessage::NOTE_ON, self::fixChannel($channel), self::fixValue($note), self::fixValue($velocity));
  }

  /**
   * @param int $channel
   * @param int $note
   * @param int $velocity
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function noteOff($channel, $note, $velocity)
  {
    return MidiMessage::shortMessage(MidiMessage::NOTE_OFF, self::fixChannel($channel), self::fixValue($note), self::fixValue($velocity));
  }

  /**
   * @param int $channel
   * @param int $controller
   * @param int $value
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function controlChange($channel, $controller, $value)
  {
    return MidiMessage::shortMessage(MidiMessage::CONTROL_CHANGE, self::fixChannel($channel), self::fixValue($controller), self::fixValue($value));
  }

  /**
   * @param int $channel
   * @param int $instrument
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function programChange($channel, $instrument)
  {
    return MidiMessage::shortMessage(MidiMessage::PROGRAM_CHANGE, self::fixChannel($channel), self::fixValue($instrument));
  }

  /**
   * @param int $channel
   * @param int $value
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */	
  public static function pitchBend($channel, $value)
  {
    return MidiMessage::shortMessage(MidiMessage::PITCH_BEND, self::fixChannel($channel), 0, self::fixValue($value));
  }

  /**
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function systemReset()
  {
    return MidiMessage::shortMessage(MidiMessage::SYSTEM_RESET);
  }

  /**
   * @param byte $usq
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function tempoInUSQ($usq)
  {
    $message = new MidiMessage(MidiMessage::TYPE_META, MidiMessage::TEMPO_CHANGE);
    $message->setData(array( (($usq >> 16) & 0xff), (($usq >> 8) & 0xff),(($usq) & 0xff) ) );
    return $message;
  }

  /**
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   * 
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function timeSignature(TimeSignature $timeSignature)
  {
    $message = new MidiMessage(MidiMessage::TYPE_META, MidiMessage::TIME_SIGNATURE_CHANGE);
    $message->setData(array( $timeSignature->getNumerator(), $timeSignature->getDenominator()->getIndex(), (96 / $timeSignature->getDenominator()->getValue()), 8));
    return $message;
  }

  /**
   * @return \PhpTabs\Reader\Midi\MidiMessage
   */
  public static function endOfTrack()
  {
    return MidiMessage::metaMessage(47, array());
  }
}
