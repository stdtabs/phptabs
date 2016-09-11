<?php

namespace PhpTabs\Writer\Midi;

class MidiTickHelper
{
  private $start;
  private $duration;

  public function __construct($start, $duration)
  {
    $this->start = $start;
    $this->duration = $duration;
  }

  public function getDuration()
  {
    return $this->duration;
  }

  public function getStart()
  {
    return $this->start;
  }
}
