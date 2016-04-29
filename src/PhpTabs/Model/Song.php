<?php

namespace PhpTabs\Model;


class Song
{
  private $name;
  private $artist;
  private $album;
  private $author;
  private $date;
  private $copyright;
  private $writer;
  private $transcriber;
  private $comments;
  private $tracks = array();
  private $measureHeaders = array();
  private $channels = array();

  public function __construct()
  {
    $this->name = '';
    $this->artist = '';
    $this->album = '';
    $this->author = '';
    $this->date = '';
    $this->copyright = '';
    $this->writer = '';
    $this->transcriber = '';
    $this->comments = '';
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getAlbum()
  {
    return $this->album;
  }

  public function setAlbum($album)
  {
    $this->album = $album;
  }

  public function getAuthor()
  {
    return $this->author;
  }

  public function setAuthor($author)
  {
    $this->author = $author;
  }

  public function getArtist()
  {
    return $this->artist;
  }

  public function setArtist($artist)
  {
    $this->artist = $artist;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function setDate($date)
  {
    $this->date = $date;
  }

  public function getCopyright()
  {
    return $this->copyright;
  }

  public function setCopyright($copyright)
  {
    $this->copyright = $copyright;
  }

  public function getWriter()
  {
    return $this->writer;
  }

  public function setWriter($writer)
  {
    $this->writer = $writer;
  }

  public function getTranscriber()
  {
    return $this->transcriber;
  }

  public function setTranscriber($transcriber)
  {
    $this->transcriber = $transcriber;
  }

  public function getComments()
  {
    return $this->comments;
  }

  public function setComments($comments)
  {
    $this->comments = $comments;
  }

  public function countMeasureHeaders()
  {
    return count($this->measureHeaders);
  }

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

  public function countTracks()
  {
    return count($this->tracks);
  }

  public function addTrack(Track $track)
  {
    $this->tracks[$this->countTracks()] = $track;
  }

  public function moveTrack($index,Track $track)
  {
    $this->removeTrack($track);
    $this->tracks[$index] = $track;
  }

  public function removeTrack(Track $track)
  {
    foreach($this->tracks as $k => $v)
      if($v->getNumber()==$track->getNumber())
        array_splice($this->tracks, $k, 1);	

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

  public function countChannels()
  {
    return count($this->channels);
  }

  public function addChannel(Channel $channel)
  {
    $this->channels[] = $channel;
  }

  public function moveChannel($index, Channel $channel)
  {
    $this->removeChannel($channel);
    $this->addChannel($channel);
  }

  public function removeChannel(Channel $channel)
  {
    $this->removeChannel($channel);
  }

  public function getChannel($index)
  {
    return isset($this->channels[$index])
      ? $this->channels[$index] : null;
  }

  public function getChannels()
  {
    return $this->channels;
  }

  public function isEmpty()
  {
    return ($this->countMeasureHeaders() == 0 || $this->countTracks() == 0);
  }

  public function clear()
  {
    $tracks = $this->getTracks();
    foreach($tracks as $k => $v)
      $this->tracks[$k]->clear();

    $channels = $this->getChannels();
    foreach($channels as $k => $v)
      $this->channels[$k]->clear();

    $measureHeaders = $this->getMeasureHeaders();
    foreach($measureHeaders as $k => $v)
      $this->measureHeaders[$k]->clear();
  }

  public function __clone()
  {
    $song = new Song();
    $song->copyFrom($this);
    return $song;
  }

  public function copyFrom(Song $song)
  {
    $this->clear();
    $this->setName($song->getName());
    $this->setArtist($song->getArtist());
    $this->setAlbum($song->getAlbum());
    $this->setAuthor($song->getAuthor());
    $this->setDate($song->getDate());
    $this->setCopyright($song->getCopyright());
    $this->setWriter($song->getWriter());
    $this->setTranscriber($song->getTranscriber());
    $this->setComments($song->getComments());

    $headers = $song->getMeasureHeaders();
    foreach($headers as $k => $v)
      $this->addMeasureHeader(clone $v);

    $channels = $song->getChannels();
    foreach($channels as $k => $v)
      $this->addMeasureChannel(clone $v);

    $tracks = $song->getTracks();
    foreach($tracks as $k => $v)
      $this->addTrack(clone $v);
  }
}
