<?php

namespace PhpTabs\Model;

/**
 * Contains one or more component notes of the chord
 */

class Chord
{
  /** @var integer $firstFret first fret id */
  private $firstFret = 0;
  
  /** @var array $strings list of strings ids */
  private $strings = array();

  /** @var string $name of the chord */
  private $name;
  
  /** @var Beat $beat which contains Chord */
  private $beat;

  /**
   * Constructor
   * 
   * @param integer $length number of strings
   * @return void
   */
  public function __construct($length)
  {
    for($i=0; $i<count($length); $i++)
    {
      $this->strings[$i] = -1;
    }
  }

  /**
   * @return Beat The chord container
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param Beat Sets the chord container
   * @return void
   */
  public function setBeat(Beat $beat)
  {
    $this->beat = $beat;
  }

  /**
   * It stacks a fret
   * @param integer $string
   * @param integer $fret
   * @return void
   */
  public function addFretValue($string, $fret)
  {
    if($string >= 0 && $string < count($this->strings))
    {
      $this->strings[$string] = $fret;
    }
  }

  /**
   * Gets a fret value by string index
   * @param integer $string Index of the stack
   * @return integer fret id
   */
  public function getFretValue($string)
  {
    if($string >= 0 && $string < count($this->strings))
    {
      return $this->strings[$string];
    }

    return -1;
  }

  /**
   * Gets the first fret id
   * @return integer first fret id
   */
  public function getFirstFret()
  {
    return $this->firstFret;
  }

  /**
   * Sets the first fret id
   * @param integer $firstFret
   * @return void
   */
  public function setFirstFret($firstFret)
  {
    $this->firstFret = $firstFret;
  }

  /**
   * Gets list of strings ids
   * @return array of strings
   */
  public function getStrings()
  {
    return $this->strings;
  }

  /**
   * Gets number of strings
   * @return integer number of strings
   */
  public function countStrings()
  {
    return count($this->strings);
  }

  /**
   * Gets number of notes which compounds the chord
   * @return integer count
   */
  public function countNotes()
  {
    $count = 0;
    for($i = 0; $i<count($this->strings); $i++)
    {
      if($this->strings[$i] >= 0)
      {
        $count++;
      }
    }

    return $count;
  }

  /**
   * Gets the chord name
   * @return string name
   */
  public function getName()
  {
    return $this->name;
  }

  /** 
   * Sets the chord name
   * @param string $name
   * @return void
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Clone current Chord
   * @return Chord clone
   */
  public function __clone()
  {
    $chord = new Chord(count($this->strings));
    $chord->setName($this->getName());
    $chord->setFirstFret($this->getFirstFret());

    for($i = 0; $i < count($chord->strings); $i++)
    {
      $chord->strings[$i] = $this->strings[$i];
    }

    return $chord;
  }
}
