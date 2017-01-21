<?php

namespace PhpTabs\Model;

/**
 * @uses Duration
 */ 
class TimeSignature
{
  private $denominator;
  private $numerator;

  public function __construct()
  {
    $this->numerator = 4;
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
   * @return \PhpTabs\Model\Duration
   */
  public function getDenominator()
  {
    return $this->denominator;
  }

  /**
   * @param \PhpTabs\Model\Duration $denominator
   */
  public function setDenominator(Duration $denominator)
  {
    $this->denominator = $denominator;
  }

  /**
   * @return \PhpTabs\Model\TimeSignature
   */
  public function __clone()
  {
    $timeSignature = new TimeSignature();
    $timeSignature->copyFrom($this);
    return $timeSignature;
  }

  /**
   * @param \PhpTabs\Model\TimeSignature $timeSignature
   */
  public function copyFrom(TimeSignature $timeSignature)
  {
    $this->setNumerator($timeSignature->getNumerator());
    $this->getDenominator()->copyFrom($timeSignature->getDenominator());
  }

  /**
   * @param \PhpTabs\Model\TimeSignature $timeSignature
   *
   * @return bool
   */
  public function isEqual(TimeSignature $timeSignature)
  {
    return $this->getNumerator() == $timeSignature->getNumerator()
        && $this->getDenominator()->isEqual($timeSignature->getDenominator());
  }
}
