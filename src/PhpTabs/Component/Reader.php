<?php

namespace PhpTabs\Component;

use Exception;

/**
 * Bridger class which routes to the right tablature parser
 * 
 * It also creates a Tablature object for later write operations
 */
class Reader
{
  /** @var Tablature object */
  private $tablature;

  /** @var ReaderInterface bridge */
  private $bridge;

  /** @var array List of extensions */
  private $extensions = array(
    'gp3'   => 'PhpTabs\\Reader\\GuitarPro\\GuitarPro3Reader',
    'gp4'   => 'PhpTabs\\Reader\\GuitarPro\\GuitarPro4Reader',
    'gp5'   => 'PhpTabs\\Reader\\GuitarPro\\GuitarPro5Reader',
    'mid'   => 'PhpTabs\\Reader\\Midi\\MidiReader',
    'midi'  => 'PhpTabs\\Reader\\Midi\\MidiReader'
  );

  /**
   * Instanciates tablature container
   * Determines which type of file
   * Try to load the right dedicated reader
   * 
   * @param File $file file which should contain a tablature
   */
  public function __construct(File $file = null)
  {
    $this->tablature = new Tablature();

    if ($file->hasError())
    {
      return;
    }

    if(isset($this->extensions[ $file->getExtension() ]))
    {
      $name = $this->extensions[ $file->getExtension() ];

      $this->bridge = new $name($file);
    }

    // Bridge not found
    if(!($this->bridge instanceof ReaderInterface))
    {
      $message = sprintf('No reader has been found for "%s" type of file'
        , $file->getExtension());

      $this->tablature->setError($message);

      throw new Exception($message); 
    }
  }

  /**
   * @return Tablature read from file tablature.
   *  Otherwise, an empty tablature with some error information
   */
  public function getTablature()
  {   
    if($this->bridge instanceof ReaderInterface)
    {
      return $this->bridge->getTablature();
    }

    return $this->tablature;  // Fallback
  }
}
