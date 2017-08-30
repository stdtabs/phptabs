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

use PhpTabs\Music\Marker;

class GuitarProMarker extends AbstractReader
{
  /**
   * Reads a measure marker
   * 
   * @param integer $measure
   *
   * @return \PhpTabs\Music\Marker
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
