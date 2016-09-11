<?php

namespace PhpTabs\Writer\Midi;

class MidiSequenceHelper
{
  private $measureHeaderHelpers;
  private $sequence;

  public function __construct(MidiSequenceHandler $sequence)
  {
    $this->sequence = $sequence;
    $this->measureHeaderHelpers = array();
  }

  public function getSequence()
  {
    return $this->sequence;
  }

  public function addMeasureHelper(MidiMeasureHelper $helper)
  {
    $this->measureHeaderHelpers[] = $helper;
  }

  public function getMeasureHelpers()
  {
    return $this->measureHeaderHelpers;
  }

  public function getMeasureHelper($index)
  {
    return $this->measureHeaderHelpers[$index];
  }
}
