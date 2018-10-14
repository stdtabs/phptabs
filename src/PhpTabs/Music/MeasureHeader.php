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

/**
 * @uses Duration
 * @uses Tempo
 * @uses TimeSignature
 */
class MeasureHeader
{
  const TRIPLET_FEEL_NONE      = 1;
  const TRIPLET_FEEL_EIGHTH    = 2;
  const TRIPLET_FEEL_SIXTEENTH = 3;

  private $number            = 0;
  private $marker            = null;
  private $repeatOpen        = false;
  private $repeatAlternative = 0;
  private $repeatClose       = 0;
  private $start;
  private $timeSignature;
  private $tempo;
  private $tripletFeel;
  private $song;

  public function __construct()
  {
    $this->start         = Duration::QUARTER_TIME;
    $this->timeSignature = new TimeSignature();
    $this->tempo         = new Tempo();
    $this->tripletFeel   = MeasureHeader::TRIPLET_FEEL_NONE;
    $this->checkMarker();
  }

  /**
   * @return int
   */
  public function getNumber()
  {
    return $this->number;
  }

  /**
   * @param int $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
    $this->checkMarker();
  }

  /**
   * @return int
   */
  public function getRepeatClose()
  {
    return $this->repeatClose;
  }

  /**
   * @param int $repeatClose
   */
  public function setRepeatClose($repeatClose)
  {
    $this->repeatClose = $repeatClose;
  }

  /**
   * @return int
   */
  public function getRepeatAlternative()
  {
    return $this->repeatAlternative;
  }

  /**
   * bitwise value 1 TO 8.
   * (1 << AlternativeNumber)
   * 
   * @param int $repeatAlternative
   */
  public function setRepeatAlternative($repeatAlternative)
  {
    $this->repeatAlternative = $repeatAlternative;
  }

  /**
   * @return bool
   */
  public function isRepeatOpen()
  {
    return $this->repeatOpen;
  }

  /**
   * @param bool|int $repeatOpen
   */
  public function setRepeatOpen($repeatOpen)
  {
    $this->repeatOpen = (boolean)$repeatOpen;
  }

  /**
   * @return int
   */
  public function getStart()
  {
    return $this->start;
  }

  /**
   * @param int $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }

  /**
   * @return int
   */
  public function getTripletFeel()
  {
    return $this->tripletFeel;
  }

  /**
   * @param int $tripletFeel
   */
  public function setTripletFeel($tripletFeel)
  {
    $this->tripletFeel = intval($tripletFeel);
  }

  /**
   * @return \PhpTabs\Music\Tempo
   */
  public function getTempo()
  {
    return $this->tempo;
  }

  /**
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function setTempo(Tempo $tempo)
  {
    $this->tempo = $tempo;
  }

  /**
   * @return \PhpTabs\Music\TimeSignature
   */
  public function getTimeSignature()
  {
    return $this->timeSignature;
  }

  /**
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   */
  public function setTimeSignature(TimeSignature $timeSignature)
  {
    $this->timeSignature = $timeSignature;
  }

  /**
   * @return null|\PhpTabs\Music\Marker
   */
  public function getMarker()
  {
    return $this->marker;
  }

  /**
   * @param null|\PhpTabs\Music\Marker $marker
   */
  public function setMarker(Marker $marker = null)
  {
    $this->marker = $marker;
  }

  /**
   * @return bool
   */
  public function hasMarker()
  {
    return $this->getMarker() !== null;
  }

  private function checkMarker()
  {
    if ($this->hasMarker()) {
      $this->marker->setMeasure($this->getNumber());
    }
  }

  /**
   * @return int
   */
  public function getLength()
  {
    return $this->getTimeSignature()->getNumerator()
         * $this->getTimeSignature()->getDenominator()->getTime();
  }

  /**
   * @return \PhpTabs\Music\Song
   */
  public function getSong()
  {
    return $this->song;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  public function setSong(Song $song)
  {
    $this->song = $song;
  }

  /**
   * @param \PhpTabs\Music\MeasureHeader $header
   */
  public function copyFrom(MeasureHeader $header)
  {
    $this->setNumber($header->getNumber());
    $this->setStart($header->getStart());
    $this->setRepeatOpen($header->isRepeatOpen());
    $this->setRepeatAlternative($header->getRepeatAlternative());
    $this->setRepeatClose($header->getRepeatClose());
    $this->setTripletFeel($header->getTripletFeel());
    $this->getTimeSignature()->copyFrom($header->getTimeSignature());
    $this->getTempo()->copyFrom($header->getTempo());
    $this->setMarker($header->hasMarker() ? clone $header->getMarker() : null);
    $this->checkMarker();
  }

  /**
   * @return \PhpTabs\Music\MeasureHeader
   */
  public function __clone()
  {
    $measureHeader = new MeasureHeader();
    $measureHeader->copyFrom($this);
    return $measureHeader;
  }
}
