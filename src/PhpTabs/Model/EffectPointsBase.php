<?php

namespace PhpTabs\Model;

class EffectPointsBase
{
  protected $position;
  protected $value;

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
}
