<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Marker;

class GuitarProMarker extends AbstractReader
{
  /**
   * Reads a measure marker
   * 
   * @param integer $measure
   *
   * @return Marker
   */
  public function readMarker($measure)
  {
    $marker = new Marker();

    $marker->setMeasure($measure);
    $marker->setTitle($this->reader->readStringByteSizeOfInteger());

    $color = new GuitarProColor();
    $color->setReader($this->reader);
    $color->readColor($marker->getColor());

    return $marker;
  }
}
