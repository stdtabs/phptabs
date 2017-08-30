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
  protected $tracks         = array();
  protected $measureHeaders = array();
  protected $channels       = array();

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
   * @return string
   */
  public function getAlbum()
  {
    return $this->album;
  }

  /**
   * @param string $album
   */
  public function setAlbum($album)
  {
    $this->album = $album;
  }

  /**
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }

  /**
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }

  /**
   * @return string
   */
  public function getArtist()
  {
    return $this->artist;
  }

  /**
   * @param string $artist
   */
  public function setArtist($artist)
  {
    $this->artist = $artist;
  }

  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }

  /**
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }

  /**
   * @return string
   */
  public function getCopyright()
  {
    return $this->copyright;
  }

  /**
   * @param string $copyright
   */
  public function setCopyright($copyright)
  {
    $this->copyright = $copyright;
  }

  /**
   * @return string
   */
  public function getWriter()
  {
    return $this->writer;
  }

  /**
   * @param string $writer
   */
  public function setWriter($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @return string
   */
  public function getTranscriber()
  {
    return $this->transcriber;
  }

  /**
   * @param string $transcriber
   */
  public function setTranscriber($transcriber)
  {
    $this->transcriber = $transcriber;
  }

  /**
   * @return string
   */
  public function getComments()
  {
    return $this->comments;
  }

  /**
   * @param string $comments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;
  }

  /**
   * @return int
   */
  public function countChannels()
  {
    return count($this->channels);
  }

  /**
   * @return int
   */
  public function countTracks()
  {
    return count($this->tracks);
  }

  /**
   * @return int
   */
  public function countMeasureHeaders()
  {
    return count($this->measureHeaders);
  }

  public function clear()
  {
    $tracks = $this->getTracks();

    foreach ($tracks as $track)
    {
      $track->clear();
    }

    $this->tracks = array();
    $this->channels = array();
    $this->measureHeaders = array();
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
    return $this->countMeasureHeaders() == 0 || $this->countTracks() == 0;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
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
    foreach ($headers as $header)
    {
      $this->addMeasureHeader(clone $header);
    }

    $channels = $song->getChannels();
    foreach ($channels as $channel)
    {
      $this->addChannel(clone $channel);
    }

    $tracks = $song->getTracks();
    foreach ($tracks as $track)
    {
      $this->addTrack(clone $track);
    }
  }
}
