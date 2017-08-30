<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component;

/**
 * Project-specific autoloader
 */
abstract class Autoloader
{
  static public function register()
  {
    spl_autoload_register(array(__CLASS__, 'autoload'), true, true);
  }

  static public function autoload($class)
  {
    $prefix = 'PhpTabs\\';

    $baseDir = dirname(__DIR__) . '/';

    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0)
    {
      return;
    }

    $relativeClass = substr($class, $len);

    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file))
    {
      require $file;
    }
  }
}
