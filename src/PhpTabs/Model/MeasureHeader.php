<?php

namespace PhpTabs\Model;

/**
 * @uses Duration
 * @uses Tempo
 * @uses TimeSignature
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
   * @param bool $repeatOpen
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
   * @return \PhpTabs\Model\Tempo
   */
  public function getTempo()
  {
    return $this->tempo;
  }

  /**
   * @param \PhpTabs\Model\Tempo $tempo
   */
  public function setTempo(Tempo $tempo)
  {
    $this->tempo = $tempo;
  }

  /**
   * @return \PhpTabs\Model\TimeSignature
   */
  public function getTimeSignature()
  {
    return $this->timeSignature;
  }

  /**
   * @param \PhpTabs\Model\TimeSignature $timeSignature
   */
  public function setTimeSignature(TimeSignature $timeSignature)
  {
    $this->timeSignature = $timeSignature;
  }

  /**
   * @return \PhpTabs\Model\Marker
   */
  public function getMarker()
  {
    return $this->marker;
  }

  /**
   * @param \PhpTabs\Model\Marker $marker
   */
  public function setMarker(Marker $marker)
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
    if ($this->hasMarker())
    {
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
   * @return \PhpTabs\Model\Song
   */
  public function getSong()
  {
    return $this->song;
  }

  /**
   * @param \PhpTabs\Model\Song $song
   */
  public function setSong(Song $song)
  {
    $this->song = $song;
  }

  /**
   * @param \PhpTabs\Model\MeasureHeader $header
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
   * @return \PhpTabs\Model\MeasureHeader
   */
  public function __clone()
  {
    $measureHeader = new MesureHeader();
    $measureHeader->copyFrom($this);
    return $measureHeader;
  }
}
