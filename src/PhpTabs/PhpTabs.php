<?php

namespace PhpTabs;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Reader;
use PhpTabs\Component\Tablature;

/**
 * High level manager
 */
class PhpTabs
{
  /** @var Tablature tablature container */
  private $tablature;

  /**
   * Initializes read/write modes
   * 
   * @param string $filename the fully qualified filename
   * @return void
   * @throws \RuntimeException When read or write failed
   */
  public function __construct($filename = null)
  {
    try
    {
      if(null === $filename)
      {
        $this->setTablature(new Tablature());
      }
      else
      {
        $reader = new Reader(new File($filename));

        $this->setTablature($reader->getTablature());
      }
    }
    catch(\Exception $e)
    {
      $message = $e->getMessage() . ' in ' . $e->getFile() 
          . ' on line ' . $e->getLine() . PHP_EOL
          . $e->getTraceAsString() . PHP_EOL;

      # if debug == true, an exception kills the process
      if(Config::get('debug'))
      {
        trigger_error($message, E_USER_ERROR);

        return;
      }

      $this->setTablature(new Tablature());
      $this->getTablature()->setError($e->getMessage());

      throw new \RuntimeException($message);
    }
  }

  /**
   * Gets the tablature instance
   *
   * @return Tablature a tablature instance
   */
  public function getTablature()
  {
    return $this->tablature;
  }

  /**
   * Sets the tablature instance
   *
   * @param Tablature $tablature a tablature instance
   */
  protected function setTablature(Tablature $tablature)
  {
    $this->tablature = $tablature;
  }

  /**
   * Overloads with $tablature methods
   * 
   * @param string $name method name
   * @param array $arguments arguments to pass
   * @return mixed
   */
  public function __call($name, $arguments)
  {
    switch(count($arguments))
    {
      case 0:
        return $this->tablature->$name();
        break;

      case 1:
        return $this->tablature->$name($arguments[0]);
        break;

      default:
        $message = sprintf(_('%s method does not support %d arguments')
            , __METHOD__, count($arguments));

        trigger_error($message, E_USER_ERROR);
        break;
    }
  }
}
