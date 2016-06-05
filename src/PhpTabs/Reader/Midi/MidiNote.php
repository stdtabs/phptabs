<?php

namespace PhpTabs\Reader\Midi;

class MidiNote
{
  private $track;
  private $channel;
  private $tick;
  private $value;
  private $velocity;
  private $pitchBends;

  public function __construct($track, $channel, $tick, $value, $velocity)
  {
    $this->track = $track;
    $this->channel = $channel;
    $this->tick = $tick;
    $this->value = $value;
    $this->velocity = $velocity;
    $this->pitchBends = array();
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

  public function getVelocity()
  {
    return $this->velocity;
  }

  public function addPitchBend($value)
  {
    $this->pitchBends[] = $value;
  }

  public function getPitchBends()
  {
    return $this->pitchBends;
  }

  public function countPitchBends()
  {
    return count($this->pitchBends);
  }
}
