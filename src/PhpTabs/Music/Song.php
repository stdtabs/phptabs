<?php

namespace PhpTabs\Music;

class Song extends SongBase
{

  /**
   * @param \PhpTabs\Music\MeasureHeader $measureHeader
   */
  public function addMeasureHeader(MeasureHeader $measureHeader)
  {
    $measureHeader->setSong($this);
    $this->measureHeaders[$this->countMeasureHeaders()] = $measureHeader;
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
    return isset($this->measureHeaders[$index])
               ? $this->measureHeaders[$index] : null;
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
    foreach ($this->tracks as $k => $v)
    {
      if ($v->getNumber() == $track->getNumber())
      {
        array_splice($this->tracks, $k, 1);	
      }
    }

    $track->clear();
  }

  /**
   * @param int $index
   *
   * @return \PhpTabs\Music\Track
   */
  public function getTrack($index)
  {
    return isset($this->tracks[$index])
               ? $this->tracks[$index] : null;
  }

  /**
   * @return array
   */
  public function getTracks()
  {
    return $this->tracks;
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\Channel $channel
   */
  public function addChannel($index, $channel = null)
  {
    if ($index instanceof Channel)
    {
      $this->channels[] = $index;
    }
    elseif (is_int($index))
    {
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
    foreach ($this-channels as $k => $v)
    {
      if ($v == $channel)
      {
        array_splice($this->channels, $k, 1);
      }
    }
  }

  /**
   * @return \PhpTabs\Music\Channel
   */
  public function getChannel($index)
  {
    return isset($this->channels[$index])
               ? $this->channels[$index] : null;
  }

  /**
   * @param  int $channelId
   * @return \PhpTabs\Music\Channel
   */
  public function getChannelById($channelId)
  {
    $channels = $this->getChannels();

    foreach ($channels as $channel)
    {
      if ($channel->getChannelId() == $channelId)
      {
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
