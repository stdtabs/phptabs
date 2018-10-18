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

use Exception;

/**
 * @uses Beat
 * @uses MeasureHeader
 * @uses Track
 */
class Measure
{
  const CLEF_TREBLE = 1;
  const CLEF_BASS   = 2;
  const CLEF_TENOR  = 3;
  const CLEF_ALTO   = 4;

  const DEFAULT_CLEF = 1;
  const DEFAULT_KEY_SIGNATURE = 0;

  private $header;
  private $track;
  private $clef;
  private $keySignature;
  private $beats = [];

  /**
   * @param \PhpTabs\Music\MeasureHeader $header
   */
  public function __construct(MeasureHeader $header)
  {
    $this->header = $header;
    $this->clef = self::DEFAULT_CLEF;
    $this->keySignature = self::DEFAULT_KEY_SIGNATURE;
  }

  /**
   * @return \PhpTabs\Music\Track
   */
  public function getTrack()
  {
    return $this->track;
  }

  /**
   * @param \PhpTabs\Music\Track $track
   */
  public function setTrack(Track $track)
  {
    $this->track = $track;
  }

  /**
   * @return int
   */
  public function getClef()
  {
    return $this->clef;
  }

  /**
   * @param int $clef
   */
  public function setClef($clef)
  {
    $this->clef = $clef;
  }

  /**
   * @return int
   */
  public function getKeySignature()
  {
    return $this->keySignature;
  }

  /**
   * @param int $keySignature
   */
  public function setKeySignature($keySignature)
  {
    $this->keySignature = $keySignature;
  }

  /**
   * @return array
   */
  public function getBeats()
  {
    return $this->beats;
  }

  /**
   * @return \PhpTabs\Music\Measure
   */
  public function addBeat(Beat $beat)
  {
    $beat->setMeasure($this);

    $this->beats[] = $beat;
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\Beat $beat
   */
  public function moveBeat($index, Beat $beat)
  {
    $this->removeBeat($beat);

    array_splice($this->beats, $index, 0, array($beat));
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
   */
  public function removeBeat(Beat $beat)
  {
    foreach ($this->beats as $k => $v)
    {
      if ($v == $beat)
      {
        array_splice($this->beats, $k, 1);

        return;
      }
    }
  }

  /**
   * @param  int $index
   * @return \PhpTabs\Music\Beat
   */
  public function getBeat($index)
  {
    if (isset($this->beats[$index])) {
      return $this->beats[$index];
    }
    
    throw new Exception(
      sprintf(
        'Index %s does not exist',
        $index
      )
    );
  }

  /**
   * @return int
   */
  public function countBeats()
  {
    return count($this->beats);
  }

  /**
   * @return \PhpTabs\Music\MeasureHeader
   */
  public function getHeader()
  {
    return $this->header;
  }

  /**
   * @param \PhpTabs\Music\MeasureHeader $header
   */
  public function setHeader(MeasureHeader $header)
  {
    $this->header = $header;
  }

  /**
   * @return int
   */
  public function getNumber()
  {
    return $this->header->getNumber();
  }

  /**
   * @return int
   */
  public function getRepeatClose()
  {
    return $this->header->getRepeatClose();
  }

  /**
   * @return int
   */
  public function getStart()
  {
    return intval($this->header->getStart());
  }

  /**
   * @return \PhpTabs\Music\Tempo
   */
  public function getTempo()
  {
    return $this->header->getTempo();
  }

  /**
   * @return \PhpTabs\Music\TimeSignature
   */
  public function getTimeSignature()
  {
    return $this->header->getTimeSignature();
  }

  /**
   * @return bool
   */
  public function isRepeatOpen()
  {
    return $this->header->isRepeatOpen();
  }

  /**
   * @return bool
   */
  public function getTripletFeel()
  {
    return $this->header->getTripletFeel();
  }

  /**
   * @return int
   */
  public function getLength()
  {
    return $this->header->getLength();
  }

  /**
   * @return \PhpTabs\Music\Marker
   */
  public function getMarker()
  {
    return $this->header->getMarker();
  }

  /**
   * @return bool
   */
  public function hasMarker()
  {
    return $this->header->hasMarker();
  }

  public function clear()
  {
    $this->beats = array();
  }

  /**
   * @param  int $start
   * @return \PhpTabs\Music\Beat
   */
  public function getBeatByStart($start)
  {
    $beat = array_reduce(
      $this->beats,
      function ($carry, $beat) use ($start) {
        return $beat->getStart() == $start
             ? $beat : $carry;
      }
    );

    if (!($beat instanceof Beat)) {
      $beat = new Beat();
      $beat->setStart($start);
      $this->addBeat($beat);
    }

    return $beat;
  }

  /**
   * @param \PhpTabs\Music\Measure $measure
   */
  public function copyFrom(Measure $measure)
  {
    $this->clear();
    $this->clef         = $measure->getClef();
    $this->keySignature = $measure->getKeySignature();

    foreach ($measure->getBeats() as $beat) {
      $this->addBeat(clone $beat);
    }
  }

  /**
   * @return \PhpTabs\Music\Measure
   */
  public function __clone()
  {
    $measure = new Measure($this->getHeader());
    $measure->copyFrom($this);

    return $measure;
  }
}
