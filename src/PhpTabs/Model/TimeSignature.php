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

  public function getNumerator()
  {
    return $this->numerator;
  }

  public function setNumerator($numerator)
  {
    $this->numerator = $numerator;
  }

  public function getDenominator()
  {
    return $this->denominator;
  }

  public function setDenominator(Duration $denominator)
  {
    $this->denominator = $denominator;
  }

  public function __clone()
  {
    $timeSignature = new TimeSignature();
    $timeSignature->copyFrom($this);
    return $timeSignature;
  }

  public function copyFrom(TimeSignature $timeSignature)
  {
    $this->setNumerator($timeSignature->getNumerator());
    $this->getDenominator()->copyFrom($timeSignature->getDenominator());
  }

  public function isEqual(TimeSignature $ts)
  {
    return ($this->getNumerator() == $ts->getNumerator() && $this->getDenominator()->isEqual($ts->getDenominator()));
  }
}
