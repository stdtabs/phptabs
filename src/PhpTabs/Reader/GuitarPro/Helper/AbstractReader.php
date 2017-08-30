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
