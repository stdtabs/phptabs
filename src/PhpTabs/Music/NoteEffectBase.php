<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

abstract class NoteEffectBase
{
  protected $vibrato              = false;
  protected $deadNote             = false;
  protected $slide                = false;
  protected $hammer               = false;
  protected $ghostNote            = false;
  protected $palmMute             = false;
  protected $staccato             = false;
  protected $tapping              = false;
  protected $slapping             = false;
  protected $popping              = false;
  protected $fadeIn               = false;
  protected $letRing              = false;
  protected $accentuatedNote      = false;
  protected $heavyAccentuatedNote = false;

  /**
   * @return bool
   */
  public function isDeadNote()
  {
    return $this->deadNote;
  }

  /**
   * @return bool
   */
  public function isVibrato()
  {
    return $this->vibrato;
  }

  /**
   * @return bool
   */
  public function isBend()
  {
    return $this->bend !== null && $this->bend->countPoints();
  }

  /**
   * @return bool
   */
  public function isTremoloBar()
  {
    return $this->tremoloBar !== null;
  }

  /**
   * @return bool
   */
  public function isTrill()
  {
    return $this->trill !== null;
  }

  /**
   * @return bool
   */
  public function isTremoloPicking()
  {
    return $this->tremoloPicking !== null;
  }

  /**
   * @return bool
   */
  public function isHammer()
  {
    return $this->hammer;
  }

  /**
   * @return bool
   */
  public function isSlide()
  {
    return $this->slide;
  }

  /**
   * @return bool
   */
  public function isGhostNote()
  {
    return $this->ghostNote;
  }

  /**
   * @return bool
   */
  public function isAccentuatedNote()
  {
    return $this->accentuatedNote;
  }

  /**
   * @return bool
   */
  public function isHeavyAccentuatedNote()
  {
    return $this->heavyAccentuatedNote;
  }

  /**
   * @return bool
   */
  public function isHarmonic()
  {
    return $this->harmonic !== null;
  }

  /**
   * @return bool
   */
  public function isGrace()
  {
    return $this->grace !== null;
  }

  /**
   * @return bool
   */
  public function isPalmMute()
  {
    return $this->palmMute;
  }

  /**
   * @return bool
   */
  public function isStaccato()
  {
    return $this->staccato;
  }

  /**
   * @return bool
   */
  public function isLetRing()
  {
    return $this->letRing;
  }

  /**
   * @return bool
   */
  public function isPopping()
  {
    return $this->popping;
  }

  /**
   * @return bool
   */
  public function isSlapping()
  {
    return $this->slapping;
  }

  /**
   * @return bool
   */
  public function isTapping()
  {
    return $this->tapping;
  }

  /**
   * @return bool
   */
  public function isFadeIn()
  {
    return $this->fadeIn;
  }

  /**
   * @return bool
   */
  public function hasAnyEffect()
  {
    return
      $this->isBend()                 ||
      $this->isTremoloBar()           ||
      $this->isHarmonic()             ||
      $this->isGrace()                ||
      $this->isTrill()                ||
      $this->isTremoloPicking()       ||
      $this->isVibrato()              ||
      $this->isDeadNote()             ||
      $this->isSlide()                ||
      $this->isHammer()               ||
      $this->isGhostNote()            ||
      $this->isAccentuatedNote()      ||
      $this->isHeavyAccentuatedNote() ||
      $this->isPalmMute()             ||
      $this->isLetRing()              ||
      $this->isStaccato()             ||
      $this->isTapping()              ||
      $this->isSlapping()             ||
      $this->isPopping()              ||
      $this->isFadeIn();
  }
}
