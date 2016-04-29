<?php

namespace PhpTabs\Model;

/**
 * @package MeasureHeader
 * @uses Duration
 * @uses TimeSignature
 * @uses Tempo
 */

class MeasureHeader
{
  const TRIPLET_FEEL_NONE = 1;
  const TRIPLET_FEEL_EIGHTH = 2;
  const TRIPLET_FEEL_SIXTEENTH = 3;

  private $number;
  private $start;
  private $timeSignature;
  private $tempo;
  private $marker;
  private $repeatOpen;
  private $repeatAlternative;
  private $repeatClose;
  private $tripletFeel;
  private $song;

  public function __construct()
  {
    $this->number = 0;
    $this->start = Duration::QUARTER_TIME;
    $this->timeSignature = new TimeSignature();
    $this->tempo = new Tempo();
    $this->marker = null;
    $this->tripletFeel = MeasureHeader::TRIPLET_FEEL_NONE;
    $this->repeatOpen = false;
    $this->repeatClose = 0;
    $this->repeatAlternative = 0;
    $this->checkMarker();
  }

  public function getNumber()
  {
    return $this->number;
  }

  public function setNumber($number)
  {
    $this->number = $number;
    $this->checkMarker();
  }

  public function getRepeatClose()
  {
    return $this->repeatClose;
  }

  public function setRepeatClose($repeatClose)
  {
    $this->repeatClose = $repeatClose;
  }

  public function getRepeatAlternative()
  {
    return $this->repeatAlternative;
  }

  /**
  * bitwise value 1 TO 8.
  * (1 << AlternativeNumber)
  */
  public function setRepeatAlternative($repeatAlternative)
  {
    $this->repeatAlternative = $repeatAlternative;
  }

  public function isRepeatOpen()
  {
    return $this->repeatOpen;
  }

  public function setRepeatOpen($repeatOpen)
  {
    $this->repeatOpen = (boolean)$repeatOpen;
  }

  public function getStart()
  {
    return $this->start;
  }

  public function setStart($start)
  {
    $this->start = $start;
  }

  public function getTripletFeel()
  {
    return $this->tripletFeel;
  }

  public function setTripletFeel($tripletFeel)
  {
    $this->tripletFeel = intval($tripletFeel);
  }

  public function getTempo()
  {
    return $this->tempo;
  }

  public function setTempo(Tempo $tempo)
  {
    $this->tempo = $tempo;
  }

  public function getTimeSignature()
  {
    return $this->timeSignature;
  }

  public function setTimeSignature(TimeSignature $timeSignature)
  {
    $this->timeSignature = $timeSignature;
  }

  public function getMarker()
  {
    return $this->marker;
  }

  public function setMarker(Marker $marker)
  {
    $this->marker = $marker;
  }

  public function hasMarker()
  {
    return ($this->getMarker() != null);
  }

  private function checkMarker()
  {
    if($this->hasMarker())
      $this->marker->setMeasure($this->getNumber());
  }

  public function getLength()
  {
    return $this->getTimeSignature()->getNumerator()
      * $this->getTimeSignature()->getDenominator()->getTime();
  }

  public function getSong()
  {
    return $this->song;
  }

  public function setSong(Song $song)
  {
    $this->song = $song;
  }

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

  public function __clone()
  {
    $measureHeader = new MesureHeader();
    $measureHeader->copyFrom($this);
    return $measureHeader;
  }
}
