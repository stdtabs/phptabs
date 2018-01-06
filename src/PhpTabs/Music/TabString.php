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

class TabString
{
  /** @var int $number */
  private $number;
  
  /** @var int $value */
  private $value;

  /**
   * @param int $number
   * @param int $value
   */
  public function __construct($number = 0, $value = 0)
  {
    $this->number = $number;
    $this->value  = $value;
  }

  /**
   * @return int
   */
  public function getNumber()
  {
    return $this->number;
  }

  /**
   * @return int
   */
  public function getValue() 
  {
    return $this->value;
  }

  /**
   * @param int $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }

  /**
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * Compares two strings
   *
   * @param \PhpTabs\Music\TabString $string the string to compare with current one
   * 
   * @return bool Result of the comparison
   */
  public function isEqual(TabString $string)
  {
    return $this->getNumber() == $string->getNumber() 
        && $this->getValue()  == $string->getValue();
  }

  /**
   * Clones current string
   * 
   * @return \PhpTabs\Music\TabString
   */
  public function __clone()
  {
    $string = new TabString();
    $string->copyFrom($this);

    return $string;
  }

  /**
   * Copies a string from another one
   *
   * @param \PhpTabs\Music\TabString $string
   */
  public function copyFrom(TabString $string)
  {
    $this->setNumber($string->getNumber());
    $this->setValue($string->getValue());
  }
}
