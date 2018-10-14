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
 * @uses BendPoint
 */
class EffectBend
{
  const SEMITONE_LENGTH     = 1;
  const MAX_POSITION_LENGTH = 12;
  const MAX_VALUE_LENGTH    = 12;

  private $points = [];

  /**
   * @param int $position
   * @param int $value
   */
  public function addPoint($position, $value)
  {
    $this->points[] = new BendPoint($position, $value);
  }

  /**
   * @return array
   */
  public function getPoints()
  {
    return $this->points;
  }

  /**
   * @return int
   */
  public function countPoints()
  {
    return count($this->points);
  }

  /**
   * @return \PhpTabs\Music\EffectBend
   */
  public function __clone()
  {
    $effect = new EffectBend();

    foreach ($this->getPoints() as $point) {
      $effect->addPoint($point->getPosition(), $point->getValue());
    }

    return $effect;
  }
}


/**
 * @uses EffectBend
 */
class BendPoint extends EffectPointsBase
{
  /**
   * @param int $duration
   * 
   * @return int
   */
  public function getTime($duration)
  {
    return intval(
      $duration * $this->getPosition() 
      / EffectBend::MAX_POSITION_LENGTH
    );
  }

  /**
   * @return \PhpTabs\Music\BendPoint
   */
  public function __clone()
  {
    return new BendPoint($this->getPosition(), $this->getValue());
  }
}
