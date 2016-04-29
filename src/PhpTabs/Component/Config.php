<?php

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
   */
  public static function get($key, $default = null)
  {
    return isset(self::$data[$key]) ? self::$data[$key] : $default;
  }


  /**
   * Sets an option
   * 
   * @param string $key option name
   * @param mixed $value optional option value
   */
  public static function set($key, $value = null)
  {
    if (is_string($key))
    {
      self::$data[$key] = $value;
    }
  }
}
