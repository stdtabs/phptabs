<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs;

use Exception;

abstract class IOFactory
{
  /**
   * Create a PhpTabs instance
   * 
   * @param  string $pathname
   * @return \PhpTabs\PhpTabs
   */
  public static function create($pathname = null)
  {
    return new PhpTabs($pathname);
  }

  /**
   * Create a PhpTabs instance from an array
   * 
   * @param  array $data
   * @return \PhpTabs\PhpTabs
   */
  public static function fromArray(array $data)
  {
    return self::create()->import($data);
  }

  /**
   * Load data from a file
   * 
   * @param  string $pathname A complete pathname
   * @param  string $type     Force a file type read
   * @return \PhpTabs\PhpTabs
   */
  public static function fromFile($pathname, $type = null)
  {
    self::checkFile($pathname);

    $type = $type === null || !is_string($type)
      ? pathinfo($pathname, PATHINFO_EXTENSION)
      : $type;

    switch (strtolower($type)) {
      case 'json':
        return self::fromJsonFile($pathname);
      case 'ser':
        return self::fromSerializedFile($pathname);
    }

    return self::create($pathname);
  }

  /**
   * Import a tablature from a PHP serialized file
   * 
   * @param  string $filename
   * @return \PhpTabs\PhpTabs
   * @throws \Exception if unserialize method failed
   */
  public static function fromSerializedFile($filename)
  {
    self::checkFile($filename);

    return self::fromSerialized(
      file_get_contents($filename)
    );
  }

  /**
   * Import a tablature from a JSON file
   * 
   * @param  string $filename
   * @return \PhpTabs\PhpTabs
   * @throws \Exception if JSON decode failed
   */
  public static function fromJsonFile($filename)
  {
    self::checkFile($filename);

    return self::fromJson(
      file_get_contents($filename)
    );
  }

  /**
   * Import a tabs from a PHP serialized string
   * 
   * @param  string $data
   * @return \PhpTabs\PhpTabs
   */
  public static function fromSerialized($data)
  {
    if (!is_string($data)) {
      throw new Exception(
        'fromSerialized() parameter must be a string'
      );
    }

    $data = version_compare(PHP_VERSION, '7.0.0', '>=')
      ? @unserialize( # Skip warning
          $data,
          ['allowed_classes' => false]
      )
      : @unserialize( # Skip warning
          $data
      );

    // unserialize failed
    if ($data === false) {
      $message = sprintf('UNSERIALIZE_FAILURE');

      throw new Exception($message);
    }

    return self::fromArray($data);
  }

  /**
   * Import a tabs from a PHP serialized string
   * 
   * @param  string $data
   * @return \PhpTabs\PhpTabs
   */
  public static function fromJson($data)
  {
    if (!is_string($data)) {
      throw new Exception('fromJson() parameter must be a string');
    }

    $data = json_decode($data, true);

    // JSON decoding error
    if (json_last_error() !== JSON_ERROR_NONE) {
      $message = sprintf(
        'JSON_DECODE_FAILURE: Error number %d - %s', 
        json_last_error(),
        function_exists('json_last_error_msg') # >= PHP 5.5.0
          ? json_last_error_msg()
          : 'See http://php.net/manual/en/function.json-last-error.php '
            . 'with your error code for more information'
      );

      throw new Exception($message);
    }

    return self::fromArray($data);
  }

  /**
   * Check that given filename is a string and is readable
   * 
   * @param  mixed $filename
   * @throws \Exception if filename is not a string 
   *  or if filename is not a file
   *  or if file is not readable
   */
  public static function checkFile($filename)
  {
    // Must be a string
    if (!is_string($filename)) {
      throw new Exception(
        "FILE_ERROR Filename must be a string. Given: "
        . gettype($filename)
      );
    }

    // Must be readable
    if (!is_readable($filename)) {
      throw new Exception(
        "FILE_ERROR Filename '$filename' is not readable"
      );
    }

    // Must be a file
    if (!is_file($filename)) {
      throw new Exception(
        "FILE_ERROR Filename '$filename' must be a file"
      );
    }
  }
}
