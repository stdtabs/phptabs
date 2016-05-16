<?php

namespace PhpTabs\Reader\Midi;

/**
 * Midi event
 */
class MidiEvent
{
  private $tick;
  private $message;

  public function __construct(MidiMessage $message, $tick)
  {
    $this->message = $message;
    $this->tick = $tick;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function getTick()
  {
    return $this->tick;
  }
}
