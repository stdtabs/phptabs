<?php

namespace PhpTabs\Model;

/**
 * @package String
 */

class String
{
  private $number;
  private $value;

  public function __construct()
  {
    $this->number = 0;
    $this->value = 0;
  }

  public function getNumber()
  {
    return $this->number;
  }

  public function getValue() 
  {
    return $this->value;
  }

  public function setNumber($number)
  {
    $this->number = $number;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function isEqual($string)
  {
    return ($this->getNumber() == $string->getNumber() 
      && $this->getValue() == $string->getValue());
  }

  public function __clone()
  {
    $string = new String();
    $string->copyFrom($this);
    return $string;
  }

  public function copyFrom(String $string)
  {
    $this->setNumber($string->getNumber());
    $this->setValue($string->getValue());
  }
}
