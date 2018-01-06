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

/**
 * @uses Duration
 */ 
class TimeSignature
{
  private $numerator = 4;
  private $denominator;

  public function __construct()
  {
    $this->denominator = new Duration();
  }

  /**
   * @return int
   */
  public function getNumerator()
  {
    return $this->numerator;
  }

  /**
   * @param int $numerator
   */
  public function setNumerator($numerator)
  {
    $this->numerator = $numerator;
  }

  /**
   * @return \PhpTabs\Music\Duration
   */
  public function getDenominator()
  {
    return $this->denominator;
  }

  /**
   * @param \PhpTabs\Music\Duration $denominator
   */
  public function setDenominator(Duration $denominator)
  {
    $this->denominator = $denominator;
  }

  /**
   * @return \PhpTabs\Music\TimeSignature
   */
  public function __clone()
  {
    $timeSignature = new TimeSignature();
    $timeSignature->copyFrom($this);
    return $timeSignature;
  }

  /**
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   */
  public function copyFrom(TimeSignature $timeSignature)
  {
    $this->setNumerator($timeSignature->getNumerator());
    $this->getDenominator()->copyFrom($timeSignature->getDenominator());
  }

  /**
   * @param  \PhpTabs\Music\TimeSignature $timeSignature
   * @return bool
   */
  public function isEqual(TimeSignature $timeSignature)
  {
    return $this->getNumerator() == $timeSignature->getNumerator()
        && $this->getDenominator()->isEqual($timeSignature->getDenominator());
  }
}
