<?php

namespace PhpTabs\Model;

/**
 * @uses Beat
 * @uses MeasureHeader
 * @uses Track
 */
class Measure
{
  const CLEF_TREBLE = 1;
  const CLEF_BASS = 2;
  const CLEF_TENOR = 3;
  const CLEF_ALTO = 4;

  const DEFAULT_CLEF = 1;
  const DEFAULT_KEY_SIGNATURE = 0;

  private $header;
  private $track;
  private $clef;
  private $keySignature;
  private $beats;

  public function __construct(MeasureHeader $header)
  {
    $this->header = $header;
    $this->clef = self::DEFAULT_CLEF;
    $this->keySignature = self::DEFAULT_KEY_SIGNATURE;
    $this->beats = array();
  }

  public function getTrack()
  {
    return $this->track;
  }

  public function setTrack(Track $track)
  {
    $this->track = $track;
  }

  public function getClef()
  {
    return $this->clef;
  }

  public function setClef($clef)
  {
    $this->clef = $clef;
  }

  public function getKeySignature()
  {
    return $this->keySignature;
  }

  public function setKeySignature($keySignature)
  {
    $this->keySignature = $keySignature;
  }

  public function getBeats()
  {
    return $this->beats;
  }

  public function addBeat(Beat $beat)
  {
    $beat->setMeasure($this);

    $this->beats[] = $beat;
  }

  public function moveBeat($index, Beat $beat)
  {
    $this->removeBeat($beat);

    array_splice($this->beats, $index, 0, array($beat));
  }

  public function removeBeat(Beat $beat)
  {
    foreach($this->beats as $k => $v)
    {
      if($v == $beat)
      {
        array_splice($this->beats, $k, 1);

        return;
      }
    }
  }

  public function getBeat($index)
  {
    if($index >= 0 && $index < $this->countBeats())
    {
      return $this->beats[$index];
    }

    return null;
  }

  public function countBeats()
  {
    return count($this->beats);
  }

  public function getHeader()
  {
    return $this->header;
  }

  public function setHeader(MeasureHeader $header)
  {
    $this->header = $header;
  }

  public function getNumber()
  {
    return $this->header->getNumber();
  }

  public function getRepeatClose()
  {
    return $this->header->getRepeatClose();
  }

  public function getStart()
  {
    return intval($this->header->getStart());
  }

  public function getTempo()
  {
    return $this->header->getTempo();
  }

  public function getTimeSignature()
  {
    return $this->header->getTimeSignature();
  }

  public function isRepeatOpen()
  {
    return $this->header->isRepeatOpen();
  }

  public function getTripletFeel()
  {
    return $this->header->getTripletFeel();
  }

  public function getLength()
  {
    return $this->header->getLength();
  }

  public function getMarker()
  {
    return $this->header->getMarker();
  }

  public function hasMarker()
  {
    return $this->header->hasMarker();
  }

  public function clear()
  {
    $this->beats = array();
  }

  public function getBeatByStart($start)
  {
    $beatCount = $this->countBeats();

    for($i = 0; $i < $beatCount; $i++)
    {
      $beat = $this->getBeat($i);

      if($beat->getStart() == $start)
      {
        return $beat;
      }
    }

    $beat = new Beat();
    $beat->setStart($start);
    $this->addBeat($beat);

    return $beat;
  }

  public function copyFrom(Measure $measure)
  {
    $this->clef = $measure->getClef();
    $this->keySignature = $measure->getKeySignature();
    $this->clear();

    for($i=0; $i<$measure->countBeats(); $i++)
    {
      $this->addBeat(clone $measure->getBeat($i));
    }
  }

  public function __clone()
  {
    $measure = new Measure($this->getHeader());
    $measure->copyFrom($this);

    return $measure;
  }
}
