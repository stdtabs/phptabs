<?php

namespace PhpTabs\Model;

/**
 * @uses \PhpTabs\Model\Duration
 */
class EffectTremoloPicking
{
  private $duration;

  public function __construct()
  {
    $this->duration = new Duration();
  }

  /**
   * @return \PhpTabs\Model\Duration
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * @param \PhpTabs\Model\Duration $duration
   */
  public function setDuration(Duration $duration)
  {
    $this->duration = $duration;
  }

  /**
   * @return \PhpTabs\Model\EffectTremoloPicking
   */
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
