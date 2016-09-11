<?php

namespace PhpTabs\Writer\Midi;

use PhpTabs\Model\TimeSignature;
use PhpTabs\Reader\Midi\MidiMessage;

class MidiMessageUtils
{
  const TICK_MOVE = 0x01;

  private static function fixValue($value)
  {
    $fixedValue = $value;
    $fixedValue = min($fixedValue, 127);
    $fixedValue = max($fixedValue, 0);

    return $fixedValue;
  }
	
  private static function fixChannel($channel)
  {
    $fixedChannel = $channel;
    $fixedChannel = min($fixedChannel, 15);
    $fixedChannel = max($fixedChannel, 0);

    return $fixedChannel;
  }
	
  public static function noteOn($channel, $note, $velocity)
  {
    return MidiMessage::shortMessage(MidiMessage::NOTE_ON, self::fixChannel($channel), self::fixValue($note), self::fixValue($velocity));
  }
	
  public static function noteOff($channel, $note, $velocity)
  {
    return MidiMessage::shortMessage(MidiMessage::NOTE_OFF, self::fixChannel($channel), self::fixValue($note), self::fixValue($velocity));
  }
	
  public static function controlChange($channel, $controller, $value)
  {
    return MidiMessage::shortMessage(MidiMessage::CONTROL_CHANGE, self::fixChannel($channel), self::fixValue($controller), self::fixValue($value));
  }
	
  public static function programChange($channel, $instrument)
  {
    return MidiMessage::shortMessage(MidiMessage::PROGRAM_CHANGE, self::fixChannel($channel), self::fixValue($instrument));
  }
	
  public static function pitchBend($channel, $value)
  {
    return MidiMessage::shortMessage(MidiMessage::PITCH_BEND, self::fixChannel($channel), 0, self::fixValue($value));
  }
	
  public static function systemReset()
  {
    return MidiMessage::shortMessage(MidiMessage::SYSTEM_RESET);
  }
	
  public static function tempoInUSQ($usq)
  {
    $message = new MidiMessage(MidiMessage::TYPE_META, MidiMessage::TEMPO_CHANGE);
    $message->setData(array( (($usq >> 16) & 0xff), (($usq >> 8) & 0xff),(($usq) & 0xff) ) );
    return $message;
  }
	
  public static function timeSignature(TimeSignature $timeSignature)
  {
    $message = new MidiMessage(MidiMessage::TYPE_META, MidiMessage::TIME_SIGNATURE_CHANGE);
    $message->setData(array( $timeSignature->getNumerator(), $timeSignature->getDenominator()->getIndex(), (96 / $timeSignature->getDenominator()->getValue()), 8));
    return $message;
  }
	
  public static function endOfTrack()
  {
    return MidiMessage::metaMessage(47, array());
  }
}
