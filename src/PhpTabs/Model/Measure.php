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

  /**
   * @param \PhpTabs\Model\MeasureHeader $header
   */
  public function __construct(MeasureHeader $header)
  {
    $this->header = $header;
    $this->clef = self::DEFAULT_CLEF;
    $this->keySignature = self::DEFAULT_KEY_SIGNATURE;
    $this->beats = array();
  }

  /**
   * @return \PhpTabs\Model\Track
   */
  public function getTrack()
  {
    return $this->track;
  }

  /**
   * @param \PhpTabs\Model\Track $track
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
   * @param int
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
   * @return \PhpTabs\Model\Measure
   */
  public function addBeat(Beat $beat)
  {
    $beat->setMeasure($this);

    $this->beats[] = $beat;
  }

  /**
   * @param int $index
   * @param \PhpTabs\Model\Beat $beat
   */
  public function moveBeat($index, Beat $beat)
  {
    $this->removeBeat($beat);

    array_splice($this->beats, $index, 0, array($beat));
  }

  /**
   * @param \PhpTabs\Model\Beat $beat
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
   * @param int $index
   *
   * @return \PhpTabs\Model\Beat
   */
  public function getBeat($index)
  {
    if ($index >= 0 && $index < $this->countBeats())
    {
      return $this->beats[$index];
    }

    return null;
  }

  /**
   * @return int
   */
  public function countBeats()
  {
    return count($this->beats);
  }

  /**
   * @return \PhpTabs\Model\MeasureHeader
   */
  public function getHeader()
  {
    return $this->header;
  }

  /**
   * @param \PhpTabs\Model\MeasureHeader $header
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
   * @return int
   */
  public function getTempo()
  {
    return $this->header->getTempo();
  }

  /**
   * @return \PhpTabs\Model\TimeSignature
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
   * @return \PhpTabs\Model\Marker
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
   * @param int $start
   * @return \PhpTabs\Model\Beat
   */
  public function getBeatByStart($start)
  {
    $beatCount = $this->countBeats();

    for ($i = 0; $i < $beatCount; $i++)
    {
      if ($this->getBeat($i)->getStart() == $start)
      {
        return $this->getBeat($i);
      }
    }

    $beat = new Beat();
    $beat->setStart($start);
    $this->addBeat($beat);

    return $beat;
  }

  /**
   * @param \PhpTabs\Model\Measure $measure
   */
  public function copyFrom(Measure $measure)
  {
    $this->clef = $measure->getClef();
    $this->keySignature = $measure->getKeySignature();
    $this->clear();

    array_walk(
      $measure->getBeats(),
      function ($beat) {
        $this->addBeat(clone $beat);
      }
    );
  }

  /**
   * @return \PhpTabs\Model\Measure
   */
  public function __clone()
  {
    $measure = new Measure($this->getHeader());
    $measure->copyFrom($this);

    return $measure;
  }
}
