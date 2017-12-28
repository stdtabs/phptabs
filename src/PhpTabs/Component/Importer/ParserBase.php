<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use Exception;

abstract class ParserBase
{
  protected $item;

  /**
   * Check that a key is set in a data array
   * 
   * @param  array        $data
   * @param  array|string $keys
   * @throws \Exception if a key is not defined
   */
  protected function checkKeys(array $data, $keys)
  {
    if (is_array($keys)) {
      foreach ($keys as $key) {
        if (!isset($data[$key]) && !array_key_exists($key, $data)) {
          throw new Exception ("Invalid data: '$key' key must be set");
        }
      }
    } elseif (is_string($keys)) {
      if (!isset($data[$keys]) && !array_key_exists($key, $data)) {
        throw new Exception ("Invalid data: '$keys' key must be set");
      }
    }
  }

  /**
   * Get parse result
   * 
   * @return mixed
   */
  public function parse()
  {
    return $this->item;
  }

  /**
   * Extends parser methods
   * 
   * @param  string $name A method name
   * @param  array  $arguments Some arguments for the method
   * @return mixed
   */
  public function __call($name, array $arguments = [])
  {
    if (strpos($name, 'parse') === 0) {
      $parserName = 
        __NAMESPACE__
        . '\\'
        . str_replace('parse', '', $name) . 'Parser';

      switch (count($arguments)) {
        case 0:
          return (new $parserName())->parse();
        case 1:
          return (new $parserName($arguments[0]))->parse();
        case 2:
          return (new $parserName($arguments[0], $arguments[1]))->parse();
      }

      $message = sprintf(
        '%s method does not support %d arguments',
        __METHOD__,
        count($arguments)
      );

      throw new Exception($message);
    }

    $message = sprintf(
      '%s method is not defined',
      $name
    );

    throw new Exception($message);
  }
}
