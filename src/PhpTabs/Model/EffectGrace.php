<?php

namespace PhpTabs\Model;

/**
 * @uses Velocities
 * @uses Duration
 */
class EffectGrace
{
  const TRANSITION_NONE = 0;
  const TRANSITION_SLIDE = 1;
  const TRANSITION_BEND = 2;
  const TRANSITION_HAMMER = 3;

  private $fret;
  private $duration;
  private $dynamic;
  private $transition;
  private $onBeat;
  private $dead;

  public function __construct()
  {
    $this->fret = 0;
    $this->duration = 1;
    $this->dynamic = Velocities::_DEFAULT;
    $this->transition = EffectGrace::TRANSITION_NONE;
    $this->onBeat = false;
    $this->dead = false;
  }

  /**
   * @return bool
   */
  public function isDead()
  {
    return $this->dead;
  }

  /**
   * @param bool $dead
   */
  public function setDead($dead)
  {
    $this->dead = $dead;
  }

  /**
   * @return int
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * @param int $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }

  /**
   * @return int
   */
  public function getDynamic()
  {
    return $this->dynamic;
  }

  /**
   * @param int $dynamic
   */
  public function setDynamic($dynamic)
  {
    $this->dynamic = $dynamic;
  }

  /**
   * @return int
   */
  public function getFret()
  {
    return $this->fret;
  }

  /**
   * @param int $fret
   */
  public function setFret($fret)
  {
    $this->fret = $fret;
  }

  /**
   * @return bool
   */
  public function isOnBeat()
  {
    return $this->onBeat;
  }

  /**
   * @param bool $onBeat
   */
  public function setOnBeat($onBeat)
  {
    $this->onBeat = $onBeat;
  }

  /**
   * @return int
   */
  public function getTransition()
  {
    return $this->transition;
  }

  /**
   * @param int $transition
   */
  public function setTransition($transition)
  {
    $this->transition = $transition;
  }

  /**
   * @return int
   */
  public function getDurationTime()
  {
    return intval((Duration::QUARTER_TIME / 16.00 ) * $this->getDuration());
  }

  /**
   * @return \PhpTabs\Model\EffectGrace
   */
  public function __clone()
  {
    $effect = new EffectGrace();
    $effect->setFret($this->getFret());
    $effect->setDuration($this->getDuration());
    $effect->setDynamic($this->getDynamic());
    $effect->setTransition($this->getTransition());
    $effect->setOnBeat($this->isOnBeat());
    $effect->setDead($this->isDead());

    return $effect;
  }
}
