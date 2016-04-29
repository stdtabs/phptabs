<?php

namespace PhpTabs\Model;

/**
 * @package EffectBend
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

    foreach($points as $k => $point)
    {
      $effect->addPoint($point->getPosition(), $point->getValue());
    }

    return $effect;
  }
}


/**
 * @package BendPoint
 */

class BendPoint
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
    return ($duration * $this->getPosition() / EffectBend::MAX_POSITION_LENGTH);
  }

  public function __clone()
  {
    return new BendPoint($this->getPosition(), $this->getValue());
  }
}
