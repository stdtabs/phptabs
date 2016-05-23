<?php

namespace PhpTabs\Model;

/**
 * @uses TremoloBarPoint
 */
class EffectTremoloBar
{
  const MAX_POSITION_LENGTH = 12;
  const MAX_VALUE_LENGTH = 12;

  private $points;

  public function __construct()
  {
    $this->points = array();
  }

  public function addPoint($position, $value)
  {
    $this->points[] = new TremoloBarPoint($position, $value);
  }

  public function getPoints()
  {
    return $this->points;
  }

  public function __clone()
  {
    $effect = new EffectTremoloBar();

    foreach($this->points as $point)
    {
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
  public function getTime($duration)
  {
    return $duration * $this->getPosition() / EffectTremoloBar::MAX_POSITION_LENGTH;
  }

  public function __clone()
  {
    return new TremoloBarPoint($this->getPosition(), $this->getValue());
  }
}
