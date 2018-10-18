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

class Song extends SongBase
{
  /**
   * @param \PhpTabs\Music\MeasureHeader $measureHeader
   */
  public function addMeasureHeader(MeasureHeader $measureHeader)
  {
    $measureHeader->setSong($this);
    $this->measureHeaders[] = $measureHeader;
  }

  /**
   * @param int $index
   */
  public function removeMeasureHeader($index)
  {
    array_splice($this->measureHeaders, $index, 1);
  }

  /**
   * @param int $index
   * 
   * @return \PhpTabs\Music\MeasureHeader
   */
  public function getMeasureHeader($index)
  {
    if (isset($this->measureHeaders[$index])) {
      return $this->measureHeaders[$index];
    }

    throw new Exception(
      sprintf(
        'Index %s does not exist',
        $index
      )
    );
  }

  /**
   * @return array
   */
  public function getMeasureHeaders()
  {
    return $this->measureHeaders;
  }

  /**
   * @param \PhpTabs\Music\Track $track
   */
  public function addTrack(Track $track)
  {
    $this->tracks[$this->countTracks()] = $track;
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\Track $track
   */
  public function moveTrack($index, Track $track)
  {
    $this->removeTrack($track);
    $this->tracks[$index] = $track;
  }

  /**
   * @param \PhpTabs\Music\Track $track
   */
  public function removeTrack(Track $track)
  {
    foreach ($this->tracks as $index => $track) {
      if ($track->getNumber() == $track->getNumber()) {
        array_splice($this->tracks, $index, 1);
        break;
      }
    }
  }

  /**
   * @param int $index
   *
   * @return \PhpTabs\Music\Track
   */
  public function getTrack($index)
  {
    if (isset($this->tracks[$index])) {
      return $this->tracks[$index];
    }

    throw new Exception(
      sprintf(
        'Index %s does not exist',
        $index
      )
    );    
  }

  /**
   * @return array
   */
  public function getTracks()
  {
    return $this->tracks;
  }

  /**
   * @param int|\PhpTabs\Music\Channel $index
   * @param \PhpTabs\Music\Channel     $channel
   */
  public function addChannel($index, Channel $channel = null)
  {
    if ($index instanceof Channel) {
      $this->channels[] = $index;
    } elseif (is_int($index)) {
      array_splice($this->channels, $index, 0, $channel);
    }
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\Channel $channel
   */
  public function moveChannel($index, Channel $channel)
  {
    $this->addChannel($index, $channel);
  }

  /**
   * @param \PhpTabs\Music\Channel $channel
   */
  public function removeChannel(Channel $channel)
  {
    foreach ($this->channels as $index => $chan) {
      if ($chan == $channel) {
        array_splice($this->channels, $index, 1);
        break;
      }
    }
  }

  /**
   * @return \PhpTabs\Music\Channel
   */
  public function getChannel($index)
  {
    if (isset($this->channels[$index])) {
      return $this->channels[$index];
    }

    throw new Exception(
      sprintf(
        'Index %s does not exist',
        $index
      )
    );
  }

  /**
   * @param  int $channelId
   * @return null|\PhpTabs\Music\Channel
   */
  public function getChannelById($channelId)
  {
    $channels = $this->getChannels();

    foreach ($channels as $channel) {
      if ($channel->getChannelId() == $channelId) {
        return $channel;
      }
    }

    return null;
  }

  /**
   * @return array
   */
  public function getChannels()
  {
    return $this->channels;
  }

  /**
   * @return \PhpTabs\Music\Song
   */
  public function __clone()
  {
    $song = new Song();
    $song->copyFrom($this);

    return $song;
  }
}
