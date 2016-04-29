<?php

namespace PhpTabs\Model;

/**
 * @package NoteEffect
 * @uses EffectBend
 * @uses EffectTremoloBar
 * @uses EffectHarmonic
 * @uses EffectGrace
 * @uses EffectTrill
 * @uses EffectTremoloPicking
 */

class NoteEffect
{
  private $bend;
	private $tremoloBar;
	private $harmonic;
	private $grace;
	private $trill;
	private $tremoloPicking;
	private $vibrato;
	private $deadNote;
	private $slide;
	private $hammer;
	private $ghostNote;
	private $accentuatedNote;
	private $heavyAccentuatedNote;
	private $palmMute;
	private $staccato;
	private $tapping;
	private $slapping;
	private $popping;
	private $fadeIn;
	private $letRing;
	
	public function __construct()
  {
		$this->bend = null;
		$this->tremoloBar = null;
		$this->harmonic = null;
		$this->grace = null;
		$this->trill = null;
		$this->tremoloPicking = null;
		$this->vibrato = false;
		$this->deadNote = false;
		$this->slide = false;
		$this->hammer = false;
		$this->ghostNote = false;
		$this->accentuatedNote = false;
		$this->heavyAccentuatedNote = false;
		$this->palmMute = false;
		$this->staccato = false;
		$this->tapping = false;
		$this->slapping = false;
		$this->popping = false;
		$this->fadeIn = false;
		$this->letRing = false;
	}
	
	public function isDeadNote()
  {
		return $this->deadNote;
	}
	
	public function setDeadNote($deadNote)
  {
		$this->deadNote = $deadNote;

		if($this->isDeadNote())
    {
			$this->bend = null;
			$this->trill = null;
			$this->slide = false;
			$this->hammer = false;
			$this->tremoloBar = null;
			$this->tremoloPicking = null;
		}
	}
	
	public function isVibrato()
  {
		return $this->vibrato;
	}
	
	public function setVibrato($vibrato)
  {
		$this->vibrato = $vibrato;

		if($this->isVibrato())
    {
			$this->trill = null;
			$this->tremoloPicking = null;
		}
	}
	
	public function getBend()
  {
		return $this->bend;
	}
	
	public function setBend(/*EffectBend*/ $bend)
  {
		$this->bend = $bend;

		if($this->isBend())
    {
			$this->trill = null;
			$this->deadNote = false;
			$this->slide = false;
			$this->hammer = false;
			$this->tremoloBar = null;
			$this->tremoloPicking = null;
		}
	}
	
	public function isBend()
  {
		return ($this->bend != null && count($this->bend->getPoints()));
	}
	
	public function getTremoloBar()
  {
		return $this->tremoloBar;
	}
	
	public function setTremoloBar(/*EffectTremoloBar*/ $tremoloBar)
  {
		$this->tremoloBar = $tremoloBar;

		if($this->isTremoloBar())
    {
			$this->bend = null;
			$this->trill = null;
			$this->deadNote = false;
			$this->slide = false;
			$this->hammer = false;
			$this->tremoloPicking = null;
		}
	}
	
	public function isTremoloBar()
  {
		return ($this->tremoloBar != null);
	}
	
	
	public function getTrill()
  {
		return $this->trill;
	}
	
	public function setTrill(/*EffectTrill*/ $trill)
  {
		$this->trill = $trill;

		if($this->isTrill())
    {
			$this->bend = null;
			$this->tremoloBar = null;
			$this->tremoloPicking = null;
			$this->slide = false;
			$this->hammer = false;
			$this->deadNote = false;
			$this->vibrato = false;
		}
	}
	
	public function isTrill()
  {
		return ($this->trill != null);
	}
	
	public function getTremoloPicking()
  {
		return $this->tremoloPicking;
	}
	
	public function setTremoloPicking(/*EffectTremoloPicking*/ $tremoloPicking)
  {
		$this->tremoloPicking = $tremoloPicking;

		if($this->isTremoloPicking())
    {
			$this->trill = null;
			$this->bend = null;
			$this->tremoloBar = null;
			$this->slide = false;
			$this->hammer = false;
			$this->deadNote = false;
			$this->vibrato = false;
		}
	}
	
	public function isTremoloPicking()
  {
		return ($this->tremoloPicking != null);
	}
	
	public function isHammer()
  {
		return $this->hammer;
	}
	
	public function setHammer($hammer)
  {
		$this->hammer = $hammer;

		if($this->isHammer())
    {
			$this->trill = null;
			$this->bend = null;
			$this->deadNote = false;
			$this->slide = false;
			$this->tremoloBar = null;
			$this->tremoloPicking = null;
		}
	}
	
	public function isSlide()
  {
		return $this->slide;
	}
	
	public function setSlide($slide)
  {
		$this->slide = $slide;

		if($this->isSlide())
    {
			$this->trill = null;
			$this->bend = null;
			$this->deadNote = false;
			$this->hammer = false;
			$this->tremoloBar = null;
			$this->tremoloPicking = null;
		}
	}
	
	public function isGhostNote()
  {
		return $this->ghostNote;
	}
	
	public function setGhostNote($ghostNote)
  {
		$this->ghostNote = $ghostNote;

		if($this->isGhostNote())
    {
			$this->accentuatedNote = false;
			$this->heavyAccentuatedNote = false;
		}
	}
	
	public function isAccentuatedNote()
  {
		return $this->accentuatedNote;
	}
	
	public function setAccentuatedNote($accentuatedNote)
  {
		$this->accentuatedNote = $accentuatedNote;

		if($this->isAccentuatedNote())
    {
			$this->ghostNote = false;
			$this->heavyAccentuatedNote = false;
		}
	}
	
	public function isHeavyAccentuatedNote()
  {
		return $this->heavyAccentuatedNote;
	}
	
	public function setHeavyAccentuatedNote($heavyAccentuatedNote)
  {
		$this->heavyAccentuatedNote = $heavyAccentuatedNote;

		if($this->isHeavyAccentuatedNote())
    {
			$this->ghostNote = false;
			$this->accentuatedNote = false;
		}
	}
	
	public function setHarmonic(/*EffectHarmonic*/ $harmonic)
  {
		$this->harmonic = $harmonic;
	}
	
	public function getHarmonic()
  {
		return $this->harmonic;
	}
	
	public function isHarmonic()
  {
		return ($this->harmonic != null);
	}
	
	public function getGrace()
  {
		return $this->grace;
	}
	
	public function setGrace(/*EffectGrace*/ $grace)
  {
		$this->grace = $grace;
	}
	
	public function isGrace()
  {
		return ($this->grace != null);
	}
	
	public function isPalmMute()
  {
		return $this->palmMute;
	}
	
	public function setPalmMute($palmMute)
  {
		$this->palmMute = $palmMute;

		if($this->isPalmMute())
    {
			$this->staccato = false;
			$this->letRing = false;
		}
	}
	
	public function isStaccato()
  {
		return $this->staccato;
	}
	
	public function setStaccato($staccato)
  {
		$this->staccato = $staccato;

		if($this->isStaccato())
    {
			$this->palmMute = false;
			$this->letRing = false;
		}
	}
	
	public function isLetRing()
  {
		return $this->letRing;
	}
	
	public function setLetRing($letRing)
  {
		$this->letRing = $letRing;

		if($this->isLetRing())
    {
			$this->staccato = false;
			$this->palmMute = false;
		}
	}
	
	public function isPopping()
  {
		return $this->popping;
	}
	
	public function setPopping($popping)
  {
		$this->popping = $popping;

		if($this->isPopping())
    {
			$this->tapping = false;
			$this->slapping = false;
		}
	}
	
	public function isSlapping()
  {
		return $this->slapping;
	}
	
	public function setSlapping($slapping)
  {
		$this->slapping = $slapping;

		if($this->isSlapping())
    {
			$this->tapping = false;
			$this->popping = false;
		}
	}
	
	public function isTapping()
  {
		return $this->tapping;
	}
	
	public function setTapping($tapping)
  {
		$this->tapping = $tapping;

		if($this->isTapping())
    {
			$this->slapping = false;
			$this->popping = false;
		}
	}
	
	public function isFadeIn()
  {
		return $this->fadeIn;
	}
	
	public function setFadeIn($fadeIn)
  {
		$this->fadeIn = $fadeIn;
	}
	
	public function hasAnyEffect()
  {
		return ($this->isBend() ||
				$this->isTremoloBar() ||
				$this->isHarmonic() ||
				$this->isGrace() ||
				$this->isTrill() ||
				$this->isTremoloPicking() ||
				$this->isVibrato() ||
				$this->isDeadNote() ||
				$this->isSlide() ||
				$this->isHammer() ||
				$this->isGhostNote() ||
				$this->isAccentuatedNote() ||
				$this->isHeavyAccentuatedNote() ||
				$this->isPalmMute() ||
				$this->isLetRing() ||
				$this->isStaccato() ||
				$this->isTapping() ||
				$this->isSlapping() ||
				$this->isPopping() ||
				$this->isFadeIn());
	}
	
	public function __clone()
  {
		$effect = new NoteEffect();
		$effect->setVibrato($this->isVibrato());
		$effect->setDeadNote($this->isDeadNote());
		$effect->setSlide($this->isSlide());
		$effect->setHammer($this->isHammer());
		$effect->setGhostNote($this->isGhostNote());
		$effect->setAccentuatedNote($this->isAccentuatedNote());
		$effect->setHeavyAccentuatedNote($this->isHeavyAccentuatedNote());
		$effect->setPalmMute($this->isPalmMute());
		$effect->setLetRing($this->isLetRing());
		$effect->setStaccato($this->isStaccato());
		$effect->setTapping($this->isTapping());
		$effect->setSlapping($this->isSlapping());
		$effect->setPopping($this->isPopping());
		$effect->setFadeIn($this->isFadeIn());
		$effect->setBend($this->isBend() ? clone $this->bend : null);
		$effect->setTremoloBar($this->isTremoloBar() ? clone $this->tremoloBar : null);
		$effect->setHarmonic($this->isHarmonic() ? clone $this->harmonic : null);
		$effect->setGrace($this->isGrace() ? clone $this->grace : null);
		$effect->setTrill($this->isTrill() ? clone $this->trill : null);
		$effect->setTremoloPicking($this->isTremoloPicking() ? clone $this->tremoloPicking : null);
		return $effect;
	}
	
}
