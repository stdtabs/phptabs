<?php

namespace PhpTabs\Reader\Midi;

/**
 * Midi message
 */
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

  public function __construct($message, $command) 
  {
    $this->message = $message;
    $this->command = $command;
  }

  public function setData($data)
  {
    $this->data = $data;
  }

  public function getData()
  {
    return $this->data;
  }

  public function getType()
  {
    return $this->message;
  }

  public function getCommand()
  {
    return $this->command;
  }

  public static function shortMessage($command, $channel = null, $data1 = null, $data2 = null)
  {
    $message = new MidiMessage(self::TYPE_SHORT, $command);

    if($channel === null && $data1 === null && $data2 = null)
    {
      $message->setData(array($command));
    }
    else if($data2 === null)
    {
      $message->setData(array(($command & 0xF0) | ($channel & 0x0F), $data1));
    }
    else
    {
      $message->setData(array(($command & 0xF0) | ($channel & 0x0F), $data1, $data2));
    }

    return $message;
  }

  public static function metaMessage($command, $data)
  {
    $message = new MidiMessage(self::TYPE_META, $command);
    $message->setData($data);
    return $message;
  }
}