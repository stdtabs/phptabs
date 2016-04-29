<?php

namespace PhpTabs\Model;

/**
 * @package EffectGrace
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

  public function isDead()
  {
    return $this->dead;
  }

  public function setDead($dead)
  {
    $this->dead = $dead;
  }

  public function getDuration()
  {
    return $this->duration;
  }

  public function setDuration($duration)
  {
    $this->duration = $duration;
  }

  public function getDynamic()
  {
    return $this->dynamic;
  }

  public function setDynamic($dynamic)
  {
    $this->dynamic = $dynamic;
  }

  public function getFret()
  {
    return $this->fret;
  }

  public function setFret($fret)
  {
    $this->fret = $fret;
  }

  public function isOnBeat()
  {
    return $this->onBeat;
  }

  public function setOnBeat($onBeat)
  {
    $this->onBeat = $onBeat;
  }

  public function getTransition()
  {
    return $this->transition;
  }

  public function setTransition($transition)
  {
    $this->transition = $transition;
  }

  public function getDurationTime()
  {
    return intval((Duration::QUARTER_TIME / 16.00 ) * $this->getDuration());
  }

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
