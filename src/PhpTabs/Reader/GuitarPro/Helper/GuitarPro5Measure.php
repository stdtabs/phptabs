<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Measure;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Track;

class GuitarPro5Measure extends AbstractReader
{
  /**
   * Reads a Measure
   * 
   * @param \PhpTabs\Music\Measure $measure
   * @param \PhpTabs\Music\Track $track
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function readMeasure(Measure $measure, Track $track, Tempo $tempo)
  {
    for ($voice = 0; $voice < 2; $voice++)
    {
      $nextNoteStart = intval($measure->getStart());
      $numberOfBeats = $this->reader->readInt();

      for ($i = 0; $i < $numberOfBeats; $i++)
      {
        $nextNoteStart += $this->reader->factory('GuitarPro5Beat')->readBeat($nextNoteStart, $measure, $track, $tempo, $voice);
      }
    }

    $emptyBeats = array();

    for ($i = 0; $i < $measure->countBeats(); $i++)
    {
      $beat = $measure->getBeat($i);
      $empty = true;

      for ($v = 0; $v < $beat->countVoices(); $v++)
      {
        if (!$beat->getVoice($v)->isEmpty())
        {
          $empty = false;
        }
      }

      if ($empty)
      {
        $emptyBeats[] = $beat;
      }
    }

    foreach ($emptyBeats as $beat)
    {
      $measure->removeBeat($beat);
    }

    $measure->setClef( $this->reader->factory('GuitarProClef')->getClef($track) );
    $measure->setKeySignature($this->reader->getKeySignature());
  }
}
