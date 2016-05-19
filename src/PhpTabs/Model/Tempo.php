<?php

namespace PhpTabs\Model;

/**
 * Tempo representations with some helpers
 */
class Tempo
{
  /** @const SECOND_IN_MILLIS */
  const SECOND_IN_MILLIS = 1000;

  /** @var integer $value Current value of the tempo */
  private $value;

  /**
   * Constructor
   * Sets a default value for tempo
   * 
   * @return void
   */
  public function __construct()
  {
    $this->value = 120;
  }

  /**
   * Gets tempo value
   *
   * @return integer
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Sets tempo value
   *
   * @param integer $value
   * @return void
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * Gets a tick in millisecond
   *
   * @return integer number of milliseconds
   */
  public function getInMillis()
  {
    return intval(60.00 / $this->getValue() * Tempo::SECOND_IN_MILLIS);
  }

  /**
   * Gets a tick in time per quarter
   * 
   * @return integer
   */
  public function getInTPQ()
  {
    return intval((60.00 / $this->getValue() * Tempo::SECOND_IN_MILLIS) * 1000.00);
  }

  /**
   * Creates a tempo from TPQ
   * 
   * @param integer $tpq
   * @return Tempo
   */
  public static function fromTPQ($tpq)
  {
    $value = ((60.00 * Tempo::SECOND_IN_MILLIS) / ($tpq / 1000.00));
    $tempo = new Tempo();
    $tempo->setValue($value);
    return $tempo;
  }

  /**
   * Clones current tempo
   *
   * @return Tempo
   */
  public function __clone()
  {
    $tempo = new Tempo();
    $tempo->copyFrom($this);
    return $tempo;
  }

  /**
   * Copies a tempo from another one
   *
   * @param Tempo $ tempo
   * @return void
   */
  public function copyFrom(Tempo $tempo)
  {
    $this->setValue($tempo->getValue());
  }
}
