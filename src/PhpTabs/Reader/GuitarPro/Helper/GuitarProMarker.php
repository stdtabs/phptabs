<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Marker;

class GuitarProMarker
{
  private $reader;

  public function __construct(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

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

    (new GuitarProColor($this->reader))->readColor($marker->getColor(), $this->reader);

    return $marker;
  }
}
