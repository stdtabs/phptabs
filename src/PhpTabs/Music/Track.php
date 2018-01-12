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

  private $number     = 0;
  private $offset     = 0;
  private $channelId  = -1;
  private $solo       = false;
  private $mute       = false;
  private $name       = '';
  private $measures   = [];
  private $strings    = [];
  private $color;
  private $lyrics;
  private $song;

  public function __construct()
  {
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
   * @param  int $index
   * @return \PhpTabs\Music\Measure
   */
  public function getMeasure($index)
  {
    return isset($this->measures[$index])
         ? $this->measures[$index] : null;
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
  public function setColor(Color $color)
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
    
    for ($i = 0; $i < $measureCount; $i++) {
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
    $track->copyFrom($this);
    return $track;
  }

  /**
   * @param \PhpTabs\Music\Track $track
   */
  public function copyFrom(Track $track)
  {
    $this->clear();
    $this->setNumber($track->getNumber());
    $this->setName($track->getName());
    $this->setOffset($track->getOffset());
    $this->setSolo($track->isSolo());
    $this->setMute($track->isMute());
    $this->setChannelId($track->getChannelId());
    $this->getColor()->copyFrom(clone $track->getColor());
    $this->getLyrics()->copyFrom(clone $track->getLyrics());

    for ($i = 0; $i < $track->countStrings(); $i++) {
      $this->strings[$i] = clone $track->getString($i + 1);
    }

    for ($i = 0; $i < $track->countMeasures(); $i++) {
      $measure = clone $track->getMeasure($i);
      $this->addMeasure(clone $measure);
    }
  }
}
