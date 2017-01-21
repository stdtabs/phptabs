<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Measure;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\Track;

class GuitarPro3Measure extends AbstractReader
{
  /**
   * Reads a Measure
   * 
   * @param \PhpTabs\Model\Measure $measure
   * @param \PhpTabs\Model\Track $track
   * @param \PhpTabs\Model\Tempo $tempo
   */
  public function readMeasure(Measure $measure, Track $track, Tempo $tempo)
  {
    $nextNoteStart = intval($measure->getStart());
    $numberOfBeats = $this->reader->readInt();

    for ($i = 0; $i < $numberOfBeats; $i++)
    {
      $nextNoteStart += $this->reader->factory('GuitarPro3Beat')->readBeat(
        $nextNoteStart,
        $measure,
        $track,
        $tempo
      );
    }

    $measure->setClef(
      $this->reader->factory('GuitarProClef')->getClef($track)
    );
    $measure->setKeySignature($this->reader->getKeySignature());
  }
}
