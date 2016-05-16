<?php

namespace PhpTabs\Reader\Midi;

class MidiChannel
{
  private $channel;
  private $instrument;
  private $volume;
  private $balance;
  private $track;

  public function __construct($channel)
  {
    $this->channel = $channel;
    $this->instrument = 0;
    $this->volume = 127;
    $this->balance = 64;
    $this->track = -1;
  }

  public function getBalance() 
  {
    return $this->balance;
  }

  public function setBalance($balance)
  {
    $this->balance = $balance;
  }

  public function getChannel()
  {
    return $this->channel;
  }

  public function getInstrument()
  {
    return $this->instrument;
  }

  public function setInstrument($instrument)
  {
    $this->instrument = $instrument;
  }

  public function getTrack()
  {
    return $this->track;
  }

  public function setTrack($track)
  {
    $this->track = $track;
  }

  public function getVolume()
  {
    return $this->volume;
  }

  public function setVolume($volume)
  {
    $this->volume = $volume;
  }
}
