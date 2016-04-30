<?php

namespace PhpTabs\Model;

/**
 * @package Track
 * @uses Measure
 * @uses TabString
 * @uses Color
 * @uses Lyric
 * @uses Song
 */

class Track
{
  const MAX_OFFSET = 24;
  const MIN_OFFSET = -24;

  private $number;
  private $offset;
  private $channelId;
  private $solo;
  private $mute;
  private $name;
  private $measures;
  private $strings;
  private $color;
  private $lyrics;
  private $song;

  public function __construct()
  {
    $this->number = 0;
    $this->offset = 0;
    $this->channelId = -1;
    $this->solo = false;
    $this->mute = false;
    $this->name = '';
    $this->measures = array();
    $this->strings = array();
    $this->color = new Color();
    $this->lyrics = new Lyric();
  }

  public function getNumber()
  {
    return $this->number;
  }

  public function setNumber($number)
  {
    $this->number = $number;
  }

  public function getMeasures()
  {
    return $this->measures;
  }

  public function addMeasure(Measure $measure)
  {
    $measure->setTrack($this);
    $this->measures[] = $measure;
  }

  public function getMeasure($index)
  {
    if($index >= 0 && $index < $this->countMeasures())
    {
      return $this->measures[$index];
    }
    return null;
  }

  public function removeMeasure($index)
  {
    array_splice($this->measures, $index, 1);
  }

  public function countMeasures()
  {
    return count($this->measures);
  }

  public function getStrings()
  {
    return $this->strings;
  }

  public function addString(TabString $string)
  {
    $this->strings[] = $string;
  }

  public function setStrings($strings)
  {
    $this->strings = $strings;
  }

  public function getColor()
  {
    return $this->color;
  }

  public function setColor($color)
  {
    $this->color = $color;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getOffset()
  {
    return $this->offset;
  }

  public function setOffset($offset)
  {
    $this->offset = $offset;
  }

  public function isSolo()
  {
    return $this->solo;
  }

  public function setSolo($solo)
  {
    $this->solo = $solo;
  }

  public function isMute()
  {
    return $this->mute;
  }

  public function setMute($mute)
  {
    $this->mute = $mute;
  }

  public function getChannelId()
  {
    return $this->channelId;
  }

  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }

  public function getLyrics()
  {
    return $this->lyrics;
  }

  public function setLyrics(Lyric $lyrics)
  {
    $this->lyrics = $lyrics;
  }

  public function getString($number)
  {
    return $this->strings[$number - 1];
  }

  public function stringCount()
  {
    return count($this->strings);
  }

  public function getSong()
  {
    return $this->song;
  }

  public function setSong(Song $song)
  {
    $this->song = $song;
  }

  public function clear()
  {
    $measureCount = $this->countMeasures();
    
    for($i=0; $i<$measureCount; $i++)
    {
      $measure = $this->getMeasure($i);
      $measure->clear();
    }

    foreach($this->strings as $k=>$v)
      $this->strings[$k] = null;

    foreach($this->measures as $k=>$v)
      $this->measures[$k]->clear();
  }

  public function __clone()
  {
    $track = new Track();
    $track->copyFrom($this->getSong, $this);
    return $track;
  }

  public function copyFrom(Song $song, Track $track)
  {
    $this->clear();
    $this->setNumber($track->getNumber());
    $this->setName($track->getName());
    $this->setOffset($track->getOffset());
    $this->setSolo($track->isSolo());
    $this->setMute($track->isMute());
    $this->setChannelId($track->getChannelId());
    $this->getColor()->copyFrom($track->getColor());
    $this->getLyrics()->copyFrom($track->getLyrics());
    for ($i=0; $i<count($track->getStrings()); $i++)
      $this->strings[$i] = clone $track->getString($i);

    for ($i=0; $i<$track->countMeasures(); $i++)
    {
      $measure = $track->getMeasure($i);
      $this->addMeasure(clone $measure($song->getMeasureHeader($i)));
    }
  }
}
