<?php

namespace PhpTabs\Model;

/**
 * @uses Duration
 */
class EffectTremoloPicking
{
  private $duration;

  public function __construct()
  {
    $this->duration = new Duration();
  }

  public function getDuration()
  {
    return $this->duration;
  }

  public function setDuration(Duration $duration)
  {
    $this->duration = $duration;
  }

  public function __clone()
  {
    $effect = new EffectTremoloPicking();

    $effect->getDuration()->setValue($this->getDuration()->getValue());
    $effect->getDuration()->setDotted($this->getDuration()->isDotted());
    $effect->getDuration()->setDoubleDotted($this->getDuration()->isDoubleDotted());
    $effect->getDuration()->getDivision()->setEnters($this->getDuration()->getDivision()->getEnters());
    $effect->getDuration()->getDivision()->setTimes($this->getDuration()->getDivision()->getTimes());

    return $effect;
  }
	
}
