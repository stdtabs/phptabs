<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;

abstract class AbstractReader
{
  protected $reader;
  protected $parserName;

  public function setReader(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

  public function setParserName($parserName)
  {
    $this->parserName = $parserName;
  }

  public function getParserName()
  {
    return $this->parserName;
  }
}
