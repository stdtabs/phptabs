<?php

namespace PhpTabs\Component;

use PhpTabs\Reader\GuitarPro\GuitarPro3Reader;
use PhpTabs\Reader\GuitarPro\GuitarPro4Reader;


/**
 * Adapter class which routes to the right tablature parser
 * 
 * It creates a Tablature object for later write operations
 */

class Reader
{
  /**
   * @var Tablature object
   */
  private $tablature;

  /**
   * @var ReaderInterface adapter
   */
  private $adapter;

  /**
   * @var array List of gp3 extensions
   */
  private $gp3Extensions = array(
    'gp3'
  );


  /**
   * Instanciates tablature container
   * Determines which type of file
   * Try to load the right dedicated reader
   * Then makes the read operations
   * 
   * @param File $file file which should contain a tablature
   */
  public function __construct(File $file = null)
  {
    $this->tablature = new Tablature();

    if ($file->hasError())
    {
      $this->tablature->setError($file->getError());

      throw new \Exception($file->getError());

      return;
    }

    // Load dedicated adapter
    if(in_array($file->getExtension(), $this->gp3Extensions))
    {
      # GuitarPro 3
      $this->adapter = new GuitarPro3Reader($file);
    }

    // Adapter not found
    if(!($this->adapter instanceof ReaderInterface))
    {
      $message = sprintf('No reader has been found for "%s" type of file'
        , $file->getExtension());

      $this->tablature->setError($message);
      
      throw new \Exception($message); 
    }
  }


  /**
   * @return Tablature read from file tablature.
   *  Otherwise, an empty tablature with some error information
   */
  public function getTablature()
  {   
    if($this->adapter instanceof ReaderInterface)
    {
      return $this->adapter->getTablature();
    }

    return $this->tablature;  // Fallback
  }

}
