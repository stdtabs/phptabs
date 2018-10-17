<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Renderer\VexTab;

use PhpTabs\Music\Beat;
use PhpTabs\Music\Note;
use PhpTabs\Music\Stroke;

class BeatContext
{
  /**
   * Referenced Beat
   * 
   * @var \PhpTabs\Music\Beat
   */
  private $beat;

  /**
   * Tuplet counter
   * 
   * @var int
   */
  private $tupletCounter = 0;

  /**
   * @var null|bool
   */
  private $isChordBeat;

  /**
   * Constructor
   * Parse beat informations for current and later usage
   * 
   * @param \PhpTabs\Music\Beat $beat
   */
  public function __construct(Beat $beat)
  {
    $this->beat = $beat;
  }

  /**
   * Should be processed as a Chord beat
   * 
   * @return bool
   */
  public function isChordBeat()
  {
    if (!is_null($this->isChordBeat)) {
      return $this->isChordBeat;
    }

    $voice = $this->beat->getVoice(0);

    if (null === $voice || !$voice->countNotes()) {
      return ($this->isChordBeat = false);
    }

    if ($voice->countNotes() > 1) {
      return ($this->isChordBeat = true);
    }

    if ($voice->getNote(0)->getEffect()->isVibrato() 
        && $this->beat->getStroke()->getDirection() !== Stroke::STROKE_NONE
    ) {
      return ($this->isChordBeat = true);
    }
  }

  /**
   * Get effects from last beat for current note
   *  - s
   *  - h
   *  - p
   *
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  public function getPrevPrefix(Note $note)
  {
    return $this->getSlide($note)
         . $this->getHammer($note);    
  }

  /**
   * Get effects that have to be prefixed for current note
   *  - t
   *  - T
   *
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  public function getPrefix(Note $note)
  {
    return $this->getTied($note)
         . $this->getTapping($note);    
  }

  /**
   * Get effects that have to be suffixed for current note
   * - b ie: 6b7b8/1
   * - v
   * - V
   * If it's a single note
   * - u
   * - d
   *
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  public function getSuffix(Note $note)
  {
    return $this->getBend($note)
         . $this->getVibrato($note)
         // . $this->getHarshVibrato($note)
         . (!$this->isChordBeat() ? $this->getStroke() : '');   
  }

  /**
   * Get suffix for a chord beat
   * - u
   * - d
   *
   * @return string
   */
  public function getChordSuffix()
  {
    return $this->getStroke();    
  }

  /**
   * return a tuplet symbol
   * 
   * @return string
   */
  public function getTuplet(BeatContext $lastBeatContext)
  {
    $enters = $this->beat
      ->getVoice(0)
      ->getDuration()
      ->getDivision()
      ->getEnters();

    if ($enters == 1) {
      return '';
    }

    $lastCounter = $lastBeatContext->getTupletCounter();

    if (++$lastCounter == $enters) {
      return sprintf(
        '^%d^ ',
        $enters
      );
    }

    $this->tupletCounter = $lastCounter;
  }

  /**
   * Get tuplet counter
   * 
   * @return int
   */
  public function getTupletCounter()
  {
    return $this->tupletCounter;
  }

  /**
   * Find corresponding string and return a slide effect if existing
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function getSlide(Note $note)
  {
    foreach ($this->beat->getVoice(0)->getNotes() as $prevNote) {
      if ($prevNote->getString() == $note->getString()) {
        return $prevNote->getEffect()->isSlide()
          ? 's' : '';
      }
    }
  }

  /**
   * Find corresponding string and return a hammer-on or a pull-off
   *  effect if existing
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function getHammer(Note $note)
  {
    foreach ($this->beat->getVoice(0)->getNotes() as $prevNote) {
      if ($prevNote->getString() == $note->getString()
       && $prevNote->getEffect()->isHammer()
      ) {
        return $prevNote->getValue() >= $note->getValue()
          ? 'p' : 'h';
      }
    }
  }

  /**
   * Return a tied symbol if existing
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function getTied(Note $note)
  {
    return $note->isTiedNote()
          ? 'T' : '';
  }

  /**
   * Return a tapping symbol if existing
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function getTapping(Note $note)
  {
    return $note->getEffect()->isTapping()
          ? 't' : '';
  }


  /**
   * Return a bend symbol if existing
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function getBend(Note $note)
  {
    if (!$note->getEffect()->isBend()) {
      return '';
    }

    $value         = '';
    $lastBendValue = $note->getValue();

    foreach ($note->getEffect()->getBend()->getPoints() as $point) {

      $bendValue = $note->getValue() + intval($point->getValue() / 2);
      //  must skip if;
      // - first bend is the same note as starting note
      // - bend is standing on the same point
      if ($bendValue != $lastBendValue) {
        $lastBendValue = $bendValue;

        $value .= sprintf(
          'b%d', 
          $bendValue
        );
      }
    }

    return $value;
  }

  /**
   * Return a vibrato symbol if existing
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function getVibrato(Note $note)
  {
    return $note->getEffect()->isVibrato()
          ? 'v' : '';
  }

  /**
   * Return a harsh vibrato symbol if existing
   * 
   * @todo implement this feature
   * 
   * @return string
   */
  private function getHarshVibrato()
  {
    return '';
  }

  /**
   * Return a stroke symbol if existing
   * 
   * @return string
   */
  private function getStroke()
  {
    if ($this->beat->getStroke()->getDirection() == Stroke::STROKE_NONE) {
      return '';
    }

    return $this->beat->getStroke()->getDirection() == Stroke::STROKE_UP
      ? 'u' : 'd';
  }
}
