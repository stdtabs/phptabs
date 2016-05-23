<?php

namespace PhpTabs\Model;

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

  public function addPoint($position, $value)
  {
    $this->points[] = new BendPoint($position, $value);
  }

  public function getPoints()
  {
    return $this->points;
  }

  public function __clone()
  {
    $effect = new EffectBend();
    $points = $this->getPoints();

    foreach($points as $point)
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
  public function getTime($duration)
  {
    return ($duration * $this->getPosition() / EffectBend::MAX_POSITION_LENGTH);
  }

  public function __clone()
  {
    return new BendPoint($this->getPosition(), $this->getValue());
  }
}
