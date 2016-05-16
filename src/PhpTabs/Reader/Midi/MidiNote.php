<?php

namespace PhpTabs\Reader\Midi;

class MidiNote
{
  private $track;
  private $channel;
  private $value;
  private $tick;

  public function __construct($track, $channel, $value, $tick)
  {
    $this->track = $track;
    $this->channel = $channel;
    $this->value = $value;
    $this->tick = $tick;
  }

  public function getChannel()
  {
    return $this->channel;
  }

  public function getTick()
  {
    return $this->tick;
  }

  public function getTrack()
  {
    return $this->track;
  }

  public function getValue()
  {
    return $this->value;
  }
}
