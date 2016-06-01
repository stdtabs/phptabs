<?php

namespace PhpTabs\Reader\GuitarPro;

use Exception;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectGrace;
use PhpTabs\Model\EffectHarmonic;
use PhpTabs\Model\EffectTrill;
use PhpTabs\Model\Lyric;
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Song;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Model\Velocities;

class GuitarPro4Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v4.00', 'FICHIER GUITAR PRO v4.06', 'FICHIER GUITAR PRO L4.06');

  /**
   * @var boolean $tripletFeel
   * @var integer $keySignature
   */  
  protected $tripletFeel, $keySignature;

  /**
   * Reader constructor
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

    $song = new Song();

    $this->setTablature($song);

    $this->factory('GuitarPro3Informations')->readInformations($song);

    $this->tripletFeel = $this->readBoolean()
      ? MeasureHeader::TRIPLET_FEEL_EIGHTH
      : MeasureHeader::TRIPLET_FEEL_NONE;

    # Meta only
    if(Config::get('type') == 'meta')
    {
      $this->closeStream();

      return;
    }

    $lyricTrack = $this->readInt();
    $lyric = $this->factory('GuitarProLyric')->readLyrics();

    $tempoValue = $this->readInt();

    $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
    $this->skip(3);

    $this->readByte();

    $channels = $this->factory('GuitarProChannels')->readChannels();

    $measures = $this->readInt();
    $tracks = $this->readInt();

    $this->readMeasureHeaders($song, $measures);
    $this->readTracks($song, $tracks, $channels, $lyric, $lyricTrack);

    # Meta+channels+tracks+measure headers only
    if(Config::get('type') == 'channels')
    {
      $this->closeStream();

      return;
    }

    $this->factory('GuitarPro3Measures')->readMeasures($song, $measures, $tracks, $tempoValue);

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
    $this->tablature->setFormat('gp4');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * Reads GraceEffect
   * 
   * @param NoteEffect $effect
   */
  private function readGrace(NoteEffect $effect)
  {
    $fret = $this->readUnsignedByte();
    $grace = new EffectGrace();
    $grace->setOnBeat(false);
    $grace->setDead( ($fret == 255) );
    $grace->setFret( ((!$grace->isDead()) ? $fret : 0) );
    $grace->setDynamic( (Velocities::MIN_VELOCITY + (Velocities::VELOCITY_INCREMENT * $this->readUnsignedByte())) - Velocities::VELOCITY_INCREMENT );
    $transition = $this->readUnsignedByte();
    if($transition == 0)
    {
      $grace->setTransition(EffectGrace::TRANSITION_NONE);
    }
    else if($transition == 1)
    {
      $grace->setTransition(EffectGrace::TRANSITION_SLIDE);
    }
    else if($transition == 2)
    {
      $grace->setTransition(EffectGrace::TRANSITION_BEND);
    }
    else if($transition == 3)
    {
      $grace->setTransition(EffectGrace::TRANSITION_HAMMER);
    }
    $grace->setDuration($this->readUnsignedByte());
    $effect->setGrace($grace);
  }

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
   * Reads NoteEffect
   * 
   * @param NoteEffect $noteEffect
   */
  public function readNoteEffects(NoteEffect $noteEffect)
  {
    $flags1 = intval($this->readUnsignedByte());
    $flags2 = intval($this->readUnsignedByte());
    $noteEffect->setHammer((($flags1 & 0x02) != 0));
    $noteEffect->setLetRing((($flags1 & 0x08) != 0));
    $noteEffect->setVibrato((($flags2 & 0x40) != 0) || $noteEffect->isVibrato());
    $noteEffect->setPalmMute((($flags2 & 0x02) != 0));
    $noteEffect->setStaccato((($flags2 & 0x01) != 0));
    if (($flags1 & 0x01) != 0)
    {
      $this->factory('GuitarPro4Effects')->readBend($noteEffect);
    }
    if (($flags1 & 0x10) != 0)
    {
      $this->readGrace($noteEffect);
    }
    if (($flags2 & 0x04) != 0)
    {
      $this->factory('GuitarPro4Effects')->readTremoloPicking($noteEffect);
    }
    if (($flags2 & 0x08) != 0)
    {
      $noteEffect->setSlide(true);
      $this->readByte();
    }
    if (($flags2 & 0x10) != 0)
    {
      $harmonic = new EffectHarmonic();
      $type = intval($this->readByte());
      if($type == 1)
      {
        $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      }
      else if($type == 3)
      {
        $harmonic->setType(EffectHarmonic::TYPE_TAPPED);
      }
      else if($type == 4)
      {
        $harmonic->setType(EffectHarmonic::TYPE_PINCH);
      }
      else if($type == 5)
      {
        $harmonic->setType(EffectHarmonic::TYPE_SEMI);
      }
      else if($type == 15)
      {
        $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
        $harmonic->setData(2);
      }
      else if($type == 17)
      {
        $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
        $harmonic->setData(3);
      }
      else if($type == 22)
      {
        $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
        $harmonic->setData(0);
      }
      $noteEffect->setHarmonic($harmonic);
    }
    if (($flags2 & 0x20) != 0)
    {
      $fret = $this->readByte();
      $period = $this->readByte();
      $trill = new EffectTrill();
      $trill->setFret($fret);
      if($period == 1)
      {
        $trill->getDuration()->setValue(Duration::SIXTEENTH);
        $noteEffect->setTrill($trill);
      }
      else if($period == 2)
      {
        $trill->getDuration()->setValue(Duration::THIRTY_SECOND);
        $noteEffect->setTrill($trill);
      }
      else if($period == 3)
      {
        $trill->getDuration()->setValue(Duration::SIXTY_FOURTH);
        $noteEffect->setTrill($trill);
      }
    }
  }

  /**
   * Loops on tracks to read
   * 
   * @param Song $song
   * @param int $count
   * @param array $channels Current array of channels
   * @param Lyric $lyric
   * @param integer $lyricTrack
   */
  private function readTracks(Song $song, $count, array $channels, Lyric $lyric, $lyricTrack)
  {
    for ($number = 1; $number <= $count; $number++)
    {
      $track = $this->factory('GuitarPro4Track')->readTrack($song, $number, $channels
        , $number == $lyricTrack ? $lyric : new Lyric());

      $song->addTrack($track);
    }
  }
}
