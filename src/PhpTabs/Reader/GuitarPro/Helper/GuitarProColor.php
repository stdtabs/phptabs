<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Color;

class GuitarProColor extends AbstractReader
{
  /**
   * Reads color informations
   * 
   * @param \PhpTabs\Model\Color $color
   */
  public function readColor(Color $color)
  {
    $color->setR($this->reader->readUnsignedByte());
    $color->setG($this->reader->readUnsignedByte());
    $color->setB($this->reader->readUnsignedByte());

    $this->reader->skip();
  }
}
