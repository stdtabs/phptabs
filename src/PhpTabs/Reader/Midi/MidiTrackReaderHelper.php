<?php

namespace PhpTabs\Reader\Midi;

/**
 * Midi track helper
 */
class MidiTrackReaderHelper
{
  public $ticks = 0;
  public $remainingBytes;
  public $runningStatusByte;

  public function __construct($ticks, $remainingBytes, $runningStatusByte)
  {
    $this->ticks = $ticks;
    $this->remainingBytes = $remainingBytes;
    $this->runningStatusByte = $runningStatusByte;
  }
}