<?php

namespace PhpTabs\Model;

class SongBase
{
  protected $name;
  protected $artist;
  protected $album;
  protected $author;
  protected $date;
  protected $copyright;
  protected $writer;
  protected $transcriber;
  protected $comments;
  protected $tracks = array();
  protected $measureHeaders = array();
  protected $channels = array();

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

  public function countChannels()
  {
    return count($this->channels);
  }

  public function countTracks()
  {
    return count($this->tracks);
  }

  public function countMeasureHeaders()
  {
    return count($this->measureHeaders);
  }

  public function clear()
  {
    $tracks = $this->getTracks();
    foreach($tracks as $track)
    {
      $track->clear();
    }

    $this->tracks = array();
    $this->channels = array();
    $this->measureHeaders = array();
  }

  public function isEmpty()
  {
    return ($this->countMeasureHeaders() == 0 || $this->countTracks() == 0);
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
    foreach($headers as $header)
    {
      $this->addMeasureHeader(clone $header);
    }

    $channels = $song->getChannels();
    foreach($channels as $channel)
    {
      $this->addChannel(clone $channel);
    }

    $tracks = $song->getTracks();
    foreach($tracks as $track)
    {
      $this->addTrack(clone $track);
    }
  }
}
