<?php

/**
 * Project specific autoloader
 *
 * Include this file only if you do NOT use composer
 * 
 * @param string $class A fully-qualified class name
 */

spl_autoload_register(function ($class)
{
  $prefix = 'PhpTabs\\';

  $baseDir = __DIR__ . '/';

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
});
