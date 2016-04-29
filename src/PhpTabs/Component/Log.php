<?php

namespace PhpTabs\Component;


abstract class Log
{
  /**
   * @var array config options
   */
  private static $data = array();


  /**
   * Add a log event
   * 
   * @param string $message Text message to log
   * @param string $type optional type of log NOTICE | WARNING | ERROR
   */
  public static function add($message, $type = 'NOTICE')
  {
    if(Config::get('verbose'))
    {
      echo PHP_EOL . "[$type] $message";
    }

    self::$data[] = array('type' => $type, 'message' => $message);
  }
}
