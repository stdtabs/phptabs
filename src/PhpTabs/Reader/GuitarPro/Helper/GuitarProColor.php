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

use PhpTabs\Music\Color;

class GuitarProColor extends AbstractReader
{
  /**
   * Reads color informations
   * 
   * @param \PhpTabs\Music\Color $color
   */
  public function readColor(Color $color)
  {
    $color->setR($this->reader->readUnsignedByte());
    $color->setG($this->reader->readUnsignedByte());
    $color->setB($this->reader->readUnsignedByte());

    $this->reader->skip();
  }
}
