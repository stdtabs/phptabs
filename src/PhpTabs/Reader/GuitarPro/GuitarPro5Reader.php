<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\GuitarPro;

use Exception;
use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\Lyric;
use PhpTabs\Music\Song;
use PhpTabs\Music\TimeSignature;

class GuitarPro5Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v5.00', 'FICHIER GUITAR PRO v5.10');

  /** @var integer $keySignature */  
  protected $keySignature;

  /**
   * @var \PhpTabs\Component\Tablature
   */
  protected $tablature;

  /**
   * @param \PhpTabs\Component\File $file An input file to read
   */
  public function __construct(File $file)
  {
    parent::__construct($file);

    $this->readVersion();

    if (!$this->isSupportedVersion($this->getVersion()))
    {
      $this->closeStream();

      throw new Exception(sprintf('Version "%s" is not supported', $this->getVersion()));
    }

    $song = new Song();

    $this->setTablature($song);

    $this->factory('GuitarPro5Informations')->readInformations($song);

    # Meta only
    if (Config::get('type') == 'meta') {
      $this->closeStream();
      return;
    }

    $lyricTrack = $this->readInt();
    $lyric = $this->factory('GuitarProLyric')->readLyrics();

    $this->readSetup();

    $tempoValue = $this->readInt();

    if ($this->getVersionIndex() > 0) {
      $this->skip(1);
    }

    $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
    $this->skip(3);

    $this->readByte();

    $channels = $this->factory('GuitarProChannels')->readChannels();

    $this->skip(42);

    $measures = $this->readInt();
    $tracks = $this->readInt();

    $this->readMeasureHeaders($song, $measures);
    $this->readTracks($song, $tracks, $channels, $lyric, $lyricTrack);

    $this->skip($this->getVersionIndex() == 0 ? 2 : 1);

    # Meta+channels+tracks+measure headers only
    if (Config::get('type') == 'channels') {
      $this->closeStream();
      return;
    }

    $this->factory('GuitarPro5Measures')->readMeasures($song, $measures, $tracks, $tempoValue);

    $this->closeStream();
  }

  /**
   * @return array An array of supported versions
   */
  public function getSupportedVersions()
  {
    return self::$supportedVersions;
  }

  /**
   * {@inheritdoc}
   */
  public function getTablature()
  {
    return isset($this->tablature)
      ? $this->tablature : new Tablature();
  }

  /**
   * Initializes Tablature with read Song
   *
   * @param \PhpTabs\Music\Song $song as read from file
   */
  private function setTablature(Song $song)
  {
    if (is_null($this->tablature))
    {
      $this->tablature = new Tablature();
    }

    $this->tablature->setSong($song);
    $this->tablature->setFormat('gp5');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * Loops on mesure headers to read
   * 
   * @param \PhpTabs\Music\Song $song
   * @param integer $count
   */
  private function readMeasureHeaders(Song $song, $count)
  {
    $timeSignature = new TimeSignature();

    for ($i = 0; $i < $count; $i++) 
    {
      if ($i > 0)
      {
        $this->skip();
      }

      $song->addMeasureHeader(
        $this->factory('GuitarPro5MeasureHeader')->readMeasureHeader($i, $timeSignature)
      );
    }
  }

  /**
   * Reads setup informations
   */
  private function readSetup()
  {
    $this->skip($this->getVersionIndex() > 0 ? 49 : 30);
    for ($i = 0; $i < 11; $i++)
    {
      $this->skip(4);
      $this->readStringByte(0);
    }
  }

  /**
   * Loops on tracks to read
   * 
   * @param \PhpTabs\Music\Song $song
   * @param int $count
   * @param array $channels array of channels
   * @param \PhpTabs\Music\Lyric $lyric
   * @param integer $lyricTrack
   */
  private function readTracks(Song $song, $count, array $channels, Lyric $lyric, $lyricTrack)
  {
    for ($number = 1; $number <= $count; $number++)
    {
      $track = $this->factory('GuitarPro5Track')->readTrack($song, $number, $channels
        , $number == $lyricTrack ? $lyric : new Lyric());

      $song->addTrack($track);
    }
  }
}
