<?php

namespace PhpTabs\Model;

abstract class NoteEffectBase
{
  protected $vibrato;
  protected $deadNote;
  protected $slide;
  protected $hammer;
  protected $ghostNote;
  protected $accentuatedNote;
  protected $heavyAccentuatedNote;
  protected $palmMute;
  protected $staccato;
  protected $tapping;
  protected $slapping;
  protected $popping;
  protected $fadeIn;
  protected $letRing;

  public function __construct()
  {
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

  public function isVibrato()
  {
    return $this->vibrato;
  }

  public function isBend()
  {
    return ($this->bend != null && count($this->bend->getPoints()));
  }

  public function isTremoloBar()
  {
    return ($this->tremoloBar != null);
  }

  public function isTrill()
  {
    return ($this->trill != null);
  }

  public function isTremoloPicking()
  {
    return ($this->tremoloPicking != null);
  }

  public function isHammer()
  {
    return $this->hammer;
  }

  public function isSlide()
  {
    return $this->slide;
  }

  public function isGhostNote()
  {
    return $this->ghostNote;
  }

  public function isAccentuatedNote()
  {
    return $this->accentuatedNote;
  }

  public function isHeavyAccentuatedNote()
  {
    return $this->heavyAccentuatedNote;
  }

  public function isHarmonic()
  {
    return ($this->harmonic != null);
  }

  public function isGrace()
  {
    return ($this->grace != null);
  }

  public function isPalmMute()
  {
    return $this->palmMute;
  }

  public function isStaccato()
  {
    return $this->staccato;
  }

  public function isLetRing()
  {
    return $this->letRing;
  }

  public function isPopping()
  {
    return $this->popping;
  }

  public function isSlapping()
  {
    return $this->slapping;
  }

  public function isTapping()
  {
    return $this->tapping;
  }

  public function isFadeIn()
  {
    return $this->fadeIn;
  }

  public function hasAnyEffect()
  {
    return (
      $this->isBend() ||
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
      $this->isFadeIn()
    );
  }	
}
