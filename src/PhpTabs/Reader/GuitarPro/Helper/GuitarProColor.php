<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Color;

class GuitarProColor
{
  /**
   * Reads color informations
   * 
   * @param Color $color
   */
  public function readColor(Color $color, GuitarProReaderInterface $reader)
  {
    $color->setR($reader->readUnsignedByte());
    $color->setG($reader->readUnsignedByte());
    $color->setB($reader->readUnsignedByte());
    $reader->skip();
  }
}
