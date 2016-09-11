<?php

namespace PhpTabs\Writer\Midi;

use PhpTabs\Model\Note;

class MidiNoteHelper
{
  private $measure;
  private $note;

  public function __construct(MidiMeasureHelper $measure, Note $note)
  {
    $this->measure = $measure;
    $this->note = $note;
  }

  public function getMeasure()
  {
    return $this->measure;
  }

  public function getNote()
  {
    return $this->note;
  }
}
