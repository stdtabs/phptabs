<?php

namespace PhpTabs\Model;

/**
 * Tablature representation of one note
 */
class TabString
{
  /** @var integer $number */
  private $number;
  
  /** @var integer $value */
  private $value;

  /**
   * Constructor
   * @return void
   */
  public function __construct()
  {
    $this->number = 0;
    $this->value = 0;
  }

  /**
   * @return integer
   */
  public function getNumber()
  {
    return $this->number;
  }

  /**
   * @return integer
   */
  public function getValue() 
  {
    return $this->value;
  }

  /**
   * @param integer $number
   * @return void
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }

  /**
   * @param integer $value
   * @return void
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * Compares two strings
   * @param TabString $string the string to compare with current one
   * @return boolean Result of the comparison
   */
  public function isEqual($string)
  {
    return ($this->getNumber() == $string->getNumber() 
      && $this->getValue() == $string->getValue());
  }

  /**
   * Clones current string
   * @return TabString
   */
  public function __clone()
  {
    $string = new TabString();
    $string->copyFrom($this);
    return $string;
  }

  /**
   * Copies a string from another one
   * @param TabString $string
   * @return void
   */
  public function copyFrom(TabString $string)
  {
    $this->setNumber($string->getNumber());
    $this->setValue($string->getValue());
  }
}
