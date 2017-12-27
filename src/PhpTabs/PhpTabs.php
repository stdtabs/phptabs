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
      $message = sprintf(
        "%s in %s on line %d\n%s\n",
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
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
   * Get PhpTabs version
   * 
   * @return string
   */
  public function getVersion()
  {
    $filename = dirname(dirname(__DIR__)) . '/composer.json';

    IOFactory::checkFile($filename);

    $composer = json_decode(
      file_get_contents($filename)
    );

    if (json_last_error() === JSON_ERROR_NONE) {
      if (isset($composer->version) && is_string($composer->version)) {
        return $composer->version;
      }
    }

    return 'Undefined';
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
    }

    $message = sprintf(
      '%s method does not support %d arguments',
      __METHOD__,
      count($arguments)
    );

    trigger_error($message, E_USER_ERROR);
  }
}
