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

use PhpTabs\Music\Duration;
use PhpTabs\Music\Measure;
use PhpTabs\Music\Song;
use PhpTabs\Music\Tempo;

class GuitarPro5Measures extends AbstractReader
{
  /**
   * Loops on mesures to read
   * 
   * @param \PhpTabs\Music\Song $song
   * @param integer $measures
   * @param integer $tracks
   * @param integer $tempoValue
   */
  public function readMeasures(Song $song, $measures, $tracks, $tempoValue)
  {
    $tempo = new Tempo();
    $tempo->setValue($tempoValue);
    $start = Duration::QUARTER_TIME;

    for ($i = 0; $i < $measures; $i++)
    {
      $header = $song->getMeasureHeader($i);
      $header->setStart($start);

      for ($j = 0; $j < $tracks; $j++)
      {
        $track = $song->getTrack($j);
        $measure = new Measure($header);

        $track->addMeasure($measure);
        $this->reader->factory('GuitarPro5Measure')->readMeasure($measure, $track, $tempo);

        if ($i != $measures - 1 || $j != $tracks - 1)
        {
          $this->reader->skip();
        }
      }

      $header->getTempo()->copyFrom($tempo);
      $start += $header->getLength();
    }
  }
}
