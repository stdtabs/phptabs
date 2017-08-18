<?php

namespace PhpTabs\Music;

/**
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
  }

  /**
   * @return array
   */
  public function getMeasures()
  {
    return $this->measures;
  }

  /**
   * @param \PhpTabs\Music\Measure $measure
   */
  public function addMeasure(Measure $measure)
  {
    $measure->setTrack($this);
    $this->measures[] = $measure;
  }

  /**
   * @param int $index
   *
   * @return \PhpTabs\Music\Measure
   */
  public function getMeasure($index)
  {
    if ($index >= 0 && $index < $this->countMeasures())
    {
      return $this->measures[$index];
    }

    return null;
  }

  /**
   * @param int $index
   */
  public function removeMeasure($index)
  {
    array_splice($this->measures, $index, 1);
  }

  /**
   * @return int
   */
  public function countMeasures()
  {
    return count($this->measures);
  }

  /**
   * @return array
   */
  public function getStrings()
  {
    return $this->strings;
  }

  /**
   * @param \PhpTabs\Music\TabString $string
   */
  public function addString(TabString $string)
  {
    $this->strings[] = $string;
  }

  /**
   * @param array $strings
   */
  public function setStrings($strings)
  {
    $this->strings = $strings;
  }

  /**
   * @return \PhpTabs\Music\Color
   */
  public function getColor()
  {
    return $this->color;
  }

  /**
   * @param \PhpTabs\Music\Color $color
   */
  public function setColor($color)
  {
    $this->color = $color;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return int
   */
  public function getOffset()
  {
    return $this->offset;
  }

  /**
   * @param int $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }

  /**
   * @return bool
   */
  public function isSolo()
  {
    return $this->solo;
  }

  /**
   * @param bool $solo
   */
  public function setSolo($solo)
  {
    $this->solo = $solo;
  }

  /**
   * @return bool
   */
  public function isMute()
  {
    return $this->mute;
  }

  /**
   * @param bool $mute
   */
  public function setMute($mute)
  {
    $this->mute = $mute;
  }

  /**
   * @return int
   */
  public function getChannelId()
  {
    return $this->channelId;
  }

  /**
   * @param int $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }

  /**
   * @return \PhpTabs\Music\Lyric
   */
  public function getLyrics()
  {
    return $this->lyrics;
  }

  /**
   * @param \PhpTabs\Music\Lyric $lyrics
   */
  public function setLyrics(Lyric $lyrics)
  {
    $this->lyrics = $lyrics;
  }

  /**
   * @return \PhpTabs\Music\TabString
   */
  public function getString($number)
  {
    return $this->strings[$number - 1];
  }

  /**
   * @return int
   */
  public function countStrings()
  {
    return count($this->strings);
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

  public function clear()
  {
    $measureCount = $this->countMeasures();
    
    for ($i = 0; $i < $measureCount; $i++)
    {
      $measure = $this->getMeasure($i);
      $measure->clear();
    }

    $this->strings = array();
    $this->measures = array();
  }

  /**
   * @return \PhpTabs\Music\Track
   */
  public function __clone()
  {
    $track = new Track();
    $track->copyFrom($this->getSong(), $this);
    return $track;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\Track $track
   */
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

    for ($i = 0; $i < $track->countStrings(); $i++)
    {
      $this->strings[$i] = clone $track->getString($i);
    }

    for ($i = 0; $i < $track->countMeasures(); $i++)
    {
      $measure = $track->getMeasure($i);
      $this->addMeasure(clone $measure($song->getMeasureHeader($i)));
    }
  }
}
