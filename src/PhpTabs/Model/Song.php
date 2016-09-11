<?php

namespace PhpTabs\Model;

class Song extends SongBase
{
  public function addMeasureHeader(MeasureHeader $measureHeader)
  {
    $measureHeader->setSong($this);
    $this->measureHeaders[$this->countMeasureHeaders()] = $measureHeader;
  }

  public function removeMeasureHeader($index)
  {
    array_splice($this->measureHeaders, $index, 1);
  }

  public function getMeasureHeader($index)
  {
    return isset($this->measureHeaders[$index])
      ? $this->measureHeaders[$index] : null;
  }

  public function getMeasureHeaders()
  {
    return $this->measureHeaders;
  }

  public function addTrack(Track $track)
  {
    $this->tracks[$this->countTracks()] = $track;
  }

  public function moveTrack($index, Track $track)
  {
    $this->removeTrack($track);
    $this->tracks[$index] = $track;
  }

  public function removeTrack(Track $track)
  {
    foreach($this->tracks as $k => $v)
    {
      if($v->getNumber() == $track->getNumber())
      {
        array_splice($this->tracks, $k, 1);	
      }
    }

    $track->clear();
  }

  public function getTrack($index)
  {
    return isset($this->tracks[$index])
      ? $this->tracks[$index] : null;
  }

  public function getTracks()
  {
    return $this->tracks;
  }

  public function addChannel($index, $channel = null)
  {
    if($index instanceof Channel)
    {
      $this->channels[] = $index;
    }
    else if(is_int($index))
    {
      array_splice($this->channels, $index, 0, $channel);
    }
  }

  public function moveChannel($index, Channel $channel)
  {
    $this->addChannel($index, $channel);
  }

  public function removeChannel(Channel $channel)
  {
    foreach($this-channels as $k => $v)
    {
      if($v == $channel)
      {
        array_splice($this->channels, $k, 1);
      }
    }
  }

  public function getChannel($index)
  {
    return isset($this->channels[$index])
      ? $this->channels[$index] : null;
  }

  public function getChannelById($channelId)
  {
    $channels = $this->getChannels();

    foreach($channels as $channel)
    {
      if($channel->getChannelId() == $channelId)
      {
        return $channel;
      }
    }

    return null;
  }

  public function getChannels()
  {
    return $this->channels;
  }

  public function __clone()
  {
    $song = new Song();
    $song->copyFrom($this);
    return $song;
  }
}
