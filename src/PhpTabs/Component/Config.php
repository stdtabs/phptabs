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

abstract class Config
{
  /**
   * @var array config options
   */
  private static $data = array();

  /**
   * Gets a defined option
   * 
   * @param string $key option name
   * @param mixed $default optional return value if not defined
   * 
   * @return mixed
   */
  public static function get($key, $default = null)
  {
    return is_string($key) && isset(self::$data[$key])
      ? self::$data[$key] : $default;
  }

  /**
   * Sets an option
   * 
   * @param string $key option name
   * @param mixed $value optional option value
   */
  public static function set($key, $value = null)
  {
    if (is_scalar($key))
    {
      self::$data[$key] = $value;
    }
  }

  /**
   * Gets all defined options
   * 
   * @return array All defined options
   */
  public static function getAll()
  {
    return self::$data;
  }

  /**
   * Delete all config values
   */
  public static function clear()
  {
    self::$data = array();
  }
}
