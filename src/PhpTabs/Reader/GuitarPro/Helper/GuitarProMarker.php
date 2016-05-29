<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Marker;
use PhpTabs\Model\Measure;

class GuitarProMarker
{
  /**
   * Reads a measure marker
   * 
   * @param integer $measure
   *
   * @return Marker
   */
  public function readMarker($measure, GuitarProReaderInterface $reader)
  {
    $marker = new Marker();

    $marker->setMeasure($measure);
    $marker->setTitle($reader->readStringByteSizeOfInteger());

    (new GuitarProColor)->readColor($marker->getColor(), $reader);

    return $marker;
  }
}
