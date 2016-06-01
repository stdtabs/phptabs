<?php

namespace PhpTabs\Reader\GuitarPro;

use Exception;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Song;
use PhpTabs\Model\TimeSignature;

class GuitarPro3Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v3.00');

  /**
   * @var boolean $tripletFeel
   * @var integer $keySignature
   */  
  protected $tripletFeel, $keySignature;

  /**
   * @param File $file input file to read
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

    $this->song = new Song();

    $this->setTablature($this->song);

    $this->factory('GuitarPro3Informations')->readInformations($this->song);

    $this->tripletFeel = $this->readBoolean()
      ? MeasureHeader::TRIPLET_FEEL_EIGHTH
      : MeasureHeader::TRIPLET_FEEL_NONE;

    $tempoValue = $this->readInt();

    $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
    $this->skip(3);

    # Meta only
    if(Config::get('type') == 'meta')
    {
      $this->closeStream();

      return;
    }

    $channels = $this->factory('GuitarProChannels')->readChannels();

    $measures = $this->readInt();
    $tracks = $this->readInt();

    $this->readMeasureHeaders($this->song, $measures);
    $this->readTracks($this->song, $tracks, $channels);

    # Meta+channels+tracks+measure headers only
    if(Config::get('type') == 'channels')
    {
      $this->closeStream();

      return;
    }

    $this->factory('GuitarPro3Measures')->readMeasures($this->song, $measures, $tracks, $tempoValue);

    $this->closeStream();
  }

  /**
   * @return array of supported versions
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
    if(isset($this->tablature))
    {
      return $this->tablature;
    }

    return new Tablature();
  }

  /**
   * Initializes Tablature with read Song
   *
   * @param Song $song as read from file
   */
  private function setTablature(Song $song)
  {
    if(!isset($this->tablature))
    {
      $this->tablature = new Tablature();
    }

    $this->tablature->setSong($song);
    $this->tablature->setFormat('gp3');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * Loops on mesure headers to read
   * 
   * @param Song $song
   * @param integer $count
   */
  private function readMeasureHeaders(Song $song, $count)
  {
    $timeSignature = new TimeSignature();

    for ($i=0; $i<$count; $i++) 
    {
      $song->addMeasureHeader($this->factory('GuitarPro3MeasureHeader')->readMeasureHeader(($i + 1), $song, $timeSignature));
    }
  }

  /**
   * Loops on tracks to read
   * 
   * @param Song $song
   * @param int $count
   * @param array $channels Current array of channels
   */
  private function readTracks(Song $song, $count, array $channels)
  {
    for ($number = 1; $number <= $count; $number++)
    {
      $song->addTrack($this->factory('GuitarPro3Track')->readTrack($song, $number, $channels));
    }
  }
}
