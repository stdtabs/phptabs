<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Measure;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\track;

class GuitarPro5Measure extends AbstractReader
{
  /**
   * Reads a Measure
   * 
   * @param Measure $measure
   * @param Track $track
   * @param Tempo $tempo
   */
  public function readMeasure(Measure $measure, Track $track, Tempo $tempo)
  {
    for($voice = 0; $voice < 2; $voice++)
    {
      $nextNoteStart = intval($measure->getStart());
      $numberOfBeats = $this->reader->readInt();

      for ($i = 0; $i < $numberOfBeats; $i++)
      {
        $nextNoteStart += $this->reader->factory('GuitarPro5Beat')->readBeat($nextNoteStart, $measure, $track, $tempo, $voice);
      }
    }

    $emptyBeats = array();

    for($i = 0; $i < $measure->countBeats(); $i++)
    {
      $beat = $measure->getBeat($i);
      $empty = true;
      for($v = 0; $v < $beat->countVoices(); $v++)
      {
        if(!$beat->getVoice($v)->isEmpty())
        {
          $empty = false;
        }
      }
      if($empty)
      {
        $emptyBeats[] = $beat;
      }
    }

    foreach($emptyBeats as $beat)
    {
      $measure->removeBeat($beat);
    }

    $measure->setClef( $this->reader->factory('GuitarProClef')->getClef($track) );
    $measure->setKeySignature($this->reader->getKeySignature());
  }
}
