<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;

abstract class AbstractReader
{
  protected $reader;
  protected $parserName;

  /**
   * @param \PhpTabs\Reader\GuitarPro\GuitarProReaderInterface $reader
   */
  public function setReader(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

  /**
   * @param string $parserName
   */
  public function setParserName($parserName)
  {
    $this->parserName = $parserName;
  }

  /**
   * @return string $parserName
   */
  public function getParserName()
  {
    return $this->parserName;
  }
}
