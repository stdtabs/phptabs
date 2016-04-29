<?php

namespace PhpTabs\Model;

/**
 * @package EffectTrill
 * @uses Duration
 */

class EffectTrill
{
	private $fret;
	private $duration;

	public function __construct()
  {
		$this->fret = 0;
		$this->duration = new Duration();
	}

	public function getFret()
  {
		return $this->fret;
	}

	public function setFret($fret)
  {
		$this->fret = $fret;
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
		$effect = new EffectTrill();

		$effect->setFret($this->getFret());
		$effect->getDuration()->setValue($this->getDuration()->getValue());
		$effect->getDuration()->setDotted($this->getDuration()->isDotted());
		$effect->getDuration()->setDoubleDotted($this->getDuration()->isDoubleDotted());
		$effect->getDuration()->getDivision()->setEnters($this->getDuration()->getDivision()->getEnters());
		$effect->getDuration()->getDivision()->setTimes($this->getDuration()->getDivision()->getTimes());

		return effect;
	}
}
