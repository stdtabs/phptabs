<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;

abstract class AbstractReader
{
  protected $reader;

  public function setReader(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }
}
