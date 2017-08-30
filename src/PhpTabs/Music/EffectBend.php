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
  const SEMITONE_LENGTH = 1;
  const MAX_POSITION_LENGTH = 12;
  const MAX_VALUE_LENGTH = 12;

  private $points;

  public function __construct()
  {
    $this->points = array();
  }

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
    $points = $this->getPoints();

    foreach ($points as $point)
    {
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
    return ($duration * $this->getPosition() / EffectBend::MAX_POSITION_LENGTH);
  }

  /**
   * @return \PhpTabs\Model\BendPoint
   */
  public function __clone()
  {
    return new BendPoint($this->getPosition(), $this->getValue());
  }
}
