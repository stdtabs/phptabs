<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

class Factory
{
  public function get($name)
  {
    $name = __NAMESPACE__ . '\\' . $name;

    return new $name();
  }
}
