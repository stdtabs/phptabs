<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;

class Factory
{
  private $reader;

  public function __construct(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

  public function get($name)
  {
    $name = __NAMESPACE__ . '\\' . $name;

    return new $name($this->reader);
  }
}
