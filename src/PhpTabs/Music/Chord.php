<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

class Chord
{
  /** @var integer $firstFret first fret id */
  private $firstFret = 0;

  /** @var array $strings list of strings ids */
  private $strings = array();

  /** @var string $name of the chord */
  private $name;

  /** @var \PhpTabs\Music\Beat $beat */
  private $beat;

  /**
   * @param int $length Number of strings
   */
  public function __construct($length)
  {
    $this->strings = array_fill(0, $length, -1);
  }

  /**
   * @return \PhpTabs\Music\Beat The chord container
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
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
    return count(
      array_filter(
        $this->strings,
        function ($value) {
          return $value >= 0;
        }
      )
    );
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
   * @return \PhpTabs\Music\Chord clone
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
