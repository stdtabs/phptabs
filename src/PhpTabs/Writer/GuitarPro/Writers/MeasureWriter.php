<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro\Writers;

use PhpTabs\Model\MeasureVoiceJoiner;
use PhpTabs\Music\Measure;
use PhpTabs\Music\Song;
use PhpTabs\Music\Tempo;

class MeasureWriter
{
  private $writer;

  public function __construct($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function writeMeasures(Song $song, Tempo $tempo)
  {
    foreach ($song->getMeasureHeaders() as $index => $header) {

      foreach ($song->getTracks() as $track) {
        $this->writeMeasure(
          $track->getMeasure($index),
          $header->getTempo()->getValue() != $tempo->getValue()
        );
      }

      $tempo->copyFrom($header->getTempo());
    }
  }

  /**
   * @param \PhpTabs\Music\Measure $srcMeasure
   * @param bool $changeTempo
   */
  private function writeMeasure(Measure $srcMeasure, $changeTempo)
  {
    $measure = (new MeasureVoiceJoiner($srcMeasure))->process();

    $beatCount = $measure->countBeats();
    $this->writer->writeInt($beatCount);

    for ($i = 0; $i < $beatCount; $i++) {
      $beat = $measure->getBeat($i);

      $this->writer->getWriter('BeatWriter')->writeBeat(
        $beat,
        $measure,
        ($changeTempo && $i == 0)
      );
    }
  }
}
