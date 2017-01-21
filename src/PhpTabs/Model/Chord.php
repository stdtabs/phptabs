<?php

namespace PhpTabs\Model;

class Chord
{
  /** @var integer $firstFret first fret id */
  private $firstFret = 0;

  /** @var array $strings list of strings ids */
  private $strings = array();

  /** @var string $name of the chord */
  private $name;

  /** @var \PhpTabs\Model\Beat $beat */
  private $beat;

  /**
   * @param int $length Number of strings
   */
  public function __construct($length)
  {
    for ($i = 0; $i < $length; $i++)
    {
      $this->strings[$i] = -1;
    }
  }

  /**
   * @return \PhpTabs\Model\Beat The chord container
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param \PhpTabs\Model\Beat $beat
   */
  public function setBeat(Beat $beat)
  {
    $this->beat = $beat;
  }

  /**
   * Puts a fret value
   *
   * @param int $string
   * @param int $fret
   */
  public function addFretValue($string, $fret)
  {
    if ($string >= 0 && $string < count($this->strings))
    {
      $this->strings[$string] = $fret;
    }
  }

  /**
   * Gets a fret value by string index
   *
   * @param int $string Index of the stack
   * 
   * @return int fret id
   */
  public function getFretValue($string)
  {
    if ($string >= 0 && $string < count($this->strings))
    {
      return $this->strings[$string];
    }

    return -1;
  }

  /**
   * Gets the first fret id
   * 
   * @return int first fret id
   */
  public function getFirstFret()
  {
    return $this->firstFret;
  }

  /**
   * Sets the first fret id
   * 
   * @param int $firstFret
   */
  public function setFirstFret($firstFret)
  {
    $this->firstFret = $firstFret;
  }

  /**
   * Gets list of strings ids
   * 
   * @return array An array of strings
   */
  public function getStrings()
  {
    return $this->strings;
  }

  /**
   * Gets number of strings
   * 
   * @return int number of strings
   */
  public function countStrings()
  {
    return count($this->strings);
  }

  /**
   * Gets number of notes which compounds the chord
   * 
   * @return int
   */
  public function countNotes()
  {
    $count = 0;

    for ($i = 0; $i < count($this->strings); $i++)
    {
      if ($this->strings[$i] >= 0)
      {
        $count++;
      }
    }

    return $count;
  }

  /**
   * Gets the chord name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /** 
   * Sets the chord name
   * 
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * Clone current Chord
   * 
   * @return \PhpTabs\Model\Chord clone
   */
  public function __clone()
  {
    $chord = new Chord(count($this->strings));
    $chord->setName($this->getName());
    $chord->setFirstFret($this->getFirstFret());

    for ($i = 0; $i < count($chord->strings); $i++)
    {
      $chord->strings[$i] = $this->strings[$i];
    }

    return $chord;
  }
}
