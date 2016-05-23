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
class TremoloBarPoint
{
  private $position;
  private $value;

  public function __construct($position, $value)
  {
    $this->position = $position;
    $this->value = $value;
  }

  public function getPosition()
  {
    return $this->position;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getTime($duration)
  {
    return $duration * $this->getPosition() / EffectTremoloBar::MAX_POSITION_LENGTH;
  }

  public function __clone()
  {
    return new TremoloBarPoint($this->getPosition(), $this->getValue());
  }
}
