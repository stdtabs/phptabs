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
 * Tempo representations with some helpers
 */
class Tempo
{
  /** @const SECOND_IN_MILLIS */
  const SECOND_IN_MILLIS = 1000;

  /** @var int $value Current value of the tempo */
  private $value = 120;

  /**
   * Gets tempo value
   *
   * @return int
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Sets tempo value
   *
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * Gets a tick in millisecond
   *
   * @return int Number of milliseconds
   */
  public function getInMillis()
  {
    return intval(60 / $this->getValue() * Tempo::SECOND_IN_MILLIS);
  }

  /**
   * Gets a tick in time per quarter
   * 
   * @return int
   */
  public function getInTPQ()
  {
    return intval((60 / $this->getValue() * Tempo::SECOND_IN_MILLIS) * 1000);
  }

  /**
   * Creates a tempo from TPQ
   * 
   * @param int $tpq
   * 
   * @return \PhpTabs\Music\Tempo
   */
  public static function fromTPQ($tpq)
  {
    $value = intval((60 * Tempo::SECOND_IN_MILLIS) / ($tpq / 1000));
    $tempo = new Tempo();
    $tempo->setValue($value);
    return $tempo;
  }

  /**
   * Clones current tempo
   *
   * @return \PhpTabs\Music\Tempo
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
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function copyFrom(Tempo $tempo)
  {
    $this->setValue($tempo->getValue());
  }
}
