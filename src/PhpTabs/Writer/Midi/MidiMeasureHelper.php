<?php

namespace PhpTabs\Writer\Midi;

class MidiMeasureHelper
{
  private $index;
  private $move;

  public function __construct($index, $move)
  {
    $this->index = $index;
    $this->move = $move;
  }

  public function getIndex()
  {
    return $this->index;
  }

  public function getMove()
  {
    return $this->move;
  }
}
