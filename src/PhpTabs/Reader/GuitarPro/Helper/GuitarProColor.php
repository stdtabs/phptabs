<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Color;

class GuitarProColor
{
  private $reader;

  public function __construct(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

  /**
   * Reads color informations
   * 
   * @param Color $color
   */
  public function readColor(Color $color)
  {
    $color->setR($this->reader->readUnsignedByte());
    $color->setG($this->reader->readUnsignedByte());
    $color->setB($this->reader->readUnsignedByte());
    $this->reader->skip();
  }
}
