<?php

namespace PhpTabs\Component;

use PhpTabs\Reader\GuitarPro\GuitarPro3Reader;
use PhpTabs\Reader\GuitarPro\GuitarPro4Reader;
use PhpTabs\Reader\GuitarPro\GuitarPro5Reader;
use PhpTabs\Reader\Midi\MidiReader;

/**
 * Bridge class which routes to the right tablature parser
 * 
 * It also creates a Tablature object for later write operations
 */
class Reader
{
  /** @var Tablature object */
  private $tablature;

  /** @var ReaderInterface bridge */
  private $bridge;

  /** @var array List of gp3 extensions */
  private $gp3Extensions = array(
    'gp3'
  );

  /* @var array List of gp4 extensions */
  private $gp4Extensions = array(
    'gp4'
  );

  /* @var array List of gp5 extensions */
  private $gp5Extensions = array(
    'gp5'
  );

  /* @var array List of MIDI extensions */
  private $midiExtensions = array(
    'mid', 'midi'
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

    // Guitar Pro 3
    if(in_array($file->getExtension(), $this->gp3Extensions))
    {
      $this->bridge = new GuitarPro3Reader($file);
    }

    // Guitar Pro 4
    if(in_array($file->getExtension(), $this->gp4Extensions))
    {
      $this->bridge = new GuitarPro4Reader($file);
    }

    // Guitar Pro 5
    if(in_array($file->getExtension(), $this->gp5Extensions))
    {
      $this->bridge = new GuitarPro5Reader($file);
    }

    // MIDI
    if(in_array($file->getExtension(), $this->midiExtensions))
    {
      $this->bridge = new MidiReader($file);
    }

    // Adapter not found
    if(!($this->bridge instanceof ReaderInterface))
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
    if($this->bridge instanceof ReaderInterface)
    {
      return $this->bridge->getTablature();
    }

    return $this->tablature;  // Fallback
  }
}
