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
use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Importer;
use PhpTabs\Component\Reader;
use PhpTabs\Component\Tablature;

class PhpTabs
{
  /**
   * @var \PhpTabs\Component\Tablature A tablature container
   */
  private $tablature;

  /**
   * @param string $pathname A complete pathname
   */
  public function __construct($pathname = null)
  {
    try {
      if (null === $pathname) {
        $this->setTablature(new Tablature());
      } else {
        $reader = new Reader(new File($pathname));

        $this->setTablature($reader->getTablature());
      }
    } catch (Exception $e) {
      $message = sprintf('%s in %s on line %d%s'
          , $e->getMessage()
          , $e->getFile()
          , $e->getLine()
          , PHP_EOL . $e->getTraceAsString() . PHP_EOL
      );

      # if debug mode, an error kills the process
      if (Config::get('debug')) {
        trigger_error($message, E_USER_ERROR);

        return;
      }

      $this->setTablature(new Tablature());
      $this->getTablature()->setError($e->getMessage());
    }
  }

  /**
   * Gets the tablature instance
   *
   * @return \PhpTabs\Component\Tablature
   */
  public function getTablature()
  {
    return $this->tablature;
  }

  /**
   * Sets the tablature instance
   *
   * @param  \PhpTabs\Component\Tablature $tablature a tablature instance
   * @return \PhpTabs\PhpTabs
   */
  protected function setTablature(Tablature $tablature)
  {
    $this->tablature = $tablature;

    return $this;
  }

  /**
   * Import a tablature from an array
   * 
   * @param  array $data A set of data that has been exported
   * @return \PhpTabs\PhpTabs
   */
  public function import(array $data)
  {
    $importer = new Importer($data);

    $this ->setTablature(new Tablature())
          ->setSong($importer->getSong());

    return $this;
  }

  /**
   * Import a tablature from a JSON file
   * 
   * @param  string $filename
   * @return \PhpTabs\PhpTabs
   * @throws \Exception if JSON decode failed
   */
  public function fromJson($filename)
  {
    $this->checkFile($filename);

    $data = json_decode(
      file_get_contents($filename),
      true
    );

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

    return $this->import($data);
  }

  /**
   * Import a tablature from a PHP serialized file
   * 
   * @param  string $filename
   * @return \PhpTabs\PhpTabs
   * @throws \Exception if unserialize method failed
   */
  public function fromSerialized($filename)
  {
    $this->checkFile($filename);

    $data = version_compare(PHP_VERSION, '7.0.0', '>=')
      ? @unserialize( # Skip warning
          file_get_contents($filename),
          ['allowed_classes' => false]
      )
      : @unserialize( # Skip warning
          file_get_contents($filename)
      );

    // unserialize failed
    if ($data === false) {
      $message = sprintf(
        'UNSERIALIZE_FAILURE: given filename %s', 
        $filename
      );

      throw new Exception($message);
    }

    return $this->import($data);
  }

  /**
   * Check that given filename is a string and is readable
   * 
   * @param mixed $filename
   * @throws \Exception if filename is not a string 
   *  or if filename is not a file
   *  or if file is not readable
   */
  private function checkFile($filename)
  {
    // Must be a string
    if (!is_string($filename)) {
      throw new Exception(
        "Filename must be a string. Given: " . gettype($filename)
      );
    }

    // Must be readable
    if (!is_readable($filename)) {
      throw new Exception(
        "Filename '$filename' is not readable"
      );
    }

    // Must be a file
    if (!is_file($filename)) {
      throw new Exception(
        "Filename '$filename' must be a file"
      );
    }
  }

  /**
   * Overloads with $tablature methods
   * 
   * @param  string $name A method name
   * @param  array  $arguments Some arguments for the method
   * @return mixed
   */
  public function __call($name, array $arguments = [])
  {
    switch (count($arguments)) {
      case 0:
        return $this->tablature->$name();

      case 1:
        return $this->tablature->$name($arguments[0]);

      case 2:
        return $this->tablature->$name($arguments[0], $arguments[1]);

      default:
        $message = sprintf(
          '%s method does not support %d arguments',
          __METHOD__,
          count($arguments)
        );

        trigger_error($message, E_USER_ERROR);
    }
  }
}
