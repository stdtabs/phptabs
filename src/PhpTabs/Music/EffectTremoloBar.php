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
 * @uses TremoloBarPoint
 */
class EffectTremoloBar
{
  const MAX_POSITION_LENGTH = 12;
  const MAX_VALUE_LENGTH    = 12;

  private $points = [];

  /**
   * @param int $position
   * @param int $value
   */
  public function addPoint($position, $value)
  {
    $this->points[] = new TremoloBarPoint($position, $value);
  }

  /**
   * @return array
   */
  public function getPoints()
  {
    return $this->points;
  }

  /**
   * @return \PhpTabs\Music\EffectTremoloBar
   */
  public function __clone()
  {
    $effect = new EffectTremoloBar();

    foreach ($this->points as $point) {
      $effect->addPoint($point->getPosition(), $point->getValue());
    }

    return $effect;
  }
}

/**
 * @uses EffectTremoloBar
 */
class TremoloBarPoint extends EffectPointsBase
{
  /**
   * @param int $duration
   * @return int
   */
  public function getTime($duration)
  {
    return intval(
      $duration * $this->getPosition()
      / EffectTremoloBar::MAX_POSITION_LENGTH
    );
  }

  /**
   * @return \PhpTabs\Music\TremoloBarPoint
   */
  public function __clone()
  {
    return new TremoloBarPoint($this->getPosition(), $this->getValue());
  }
}
