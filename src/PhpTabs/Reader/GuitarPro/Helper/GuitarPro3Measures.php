<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Duration;
use PhpTabs\Model\Measure;
use PhpTabs\Model\Song;
use PhpTabs\Model\Tempo;

class GuitarPro3Measures extends AbstractReader
{
  /**
   * Loops on mesures to read
   * 
   * @param Song $song
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
        $this->reader->readMeasure($measure, $track, $tempo);
      }

      $header->getTempo()->copyFrom($tempo);
      $start += $header->getLength();
    }
  }
}
