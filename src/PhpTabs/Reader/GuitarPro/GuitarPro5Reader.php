<?php

namespace PhpTabs\Reader\GuitarPro;

use Exception;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Chord;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectGrace;
use PhpTabs\Model\EffectHarmonic;
use PhpTabs\Model\EffectTrill;
use PhpTabs\Model\Lyric;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Song;
use PhpTabs\Model\Stroke;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Model\Velocities;

class GuitarPro5Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v5.00', 'FICHIER GUITAR PRO v5.10');

  /** @var integer $keySignature */  
  protected $keySignature;

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

    $this->factory('GuitarPro5Informations')->readInformations($this->song);

    # Meta only
    if(Config::get('type') == 'meta')
    {
      $this->closeStream();

      return;
    }

    $lyricTrack = $this->readInt();
    $lyric = $this->readLyrics();

    $this->readSetup();

    $tempoValue = $this->readInt();

    if($this->getVersionIndex() > 0)
    {
      $this->skip(1);
    }

    $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
    $this->skip(3);

    $this->readByte();

    $channels = $this->factory('GuitarProChannels')->readChannels();

    $this->skip(42);

    $measures = $this->readInt();
    $tracks = $this->readInt();

    $this->readMeasureHeaders($this->song, $measures);
    $this->readTracks($this->song, $tracks, $channels, $lyric, $lyricTrack);

    $this->skip($this->getVersionIndex() == 0 ? 2 : 1);

    # Meta+channels+tracks+measure headers only
    if(Config::get('type') == 'channels')
    {
      $this->closeStream();

      return;
    }

    $this->factory('GuitarPro5Measures')->readMeasures($this->song, $measures, $tracks, $tempoValue);

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
    $this->tablature->setFormat('gp5');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * Reads an artificial harmonic
   * 
   * @param NoteEffect $effect
   */
  private function readArtificialHarmonic(NoteEffect $effect)
  {
    $type = $this->readByte();
    $harmonic = new EffectHarmonic();
    $harmonic->setData(0);
    if($type == 1)
    {
      $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      $effect->setHarmonic($harmonic);
    }
    else if($type == 2)
    {
      $this->skip(3);
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $effect->setHarmonic($harmonic);
    }
    else if($type == 3)
    {
      $this->skip(1);
      $harmonic->setType(EffectHarmonic::TYPE_TAPPED);
      $effect->setHarmonic($harmonic);
    }
    else if($type == 4)
    {
      $harmonic->setType(EffectHarmonic::TYPE_PINCH);
      $effect->setHarmonic($harmonic);
    }
    else if($type == 5)
    {
      $harmonic->setType(EffectHarmonic::TYPE_SEMI);
      $effect->setHarmonic($harmonic);
    }
  }

  /**
   * Reads some NoteEffect informations
   * 
   * @param Beat $beat
   * @param NoteEffect $effect
   */
  public function readBeatEffects(Beat $beat, NoteEffect $noteEffect)
  {
    $flags1 = $this->readUnsignedByte();
    $flags2 = $this->readUnsignedByte();
    $noteEffect->setFadeIn((($flags1 & 0x10) != 0));
    $noteEffect->setVibrato((($flags1 & 0x02) != 0));
    if (($flags1 & 0x20) != 0)
    {
      $effect = $this->readUnsignedByte();
      $noteEffect->setTapping($effect == 1);
      $noteEffect->setSlapping($effect == 2);
      $noteEffect->setPopping($effect == 3);
    }
    if (($flags2 & 0x04) != 0)
    {
      $this->factory('GuitarPro4Effects')->readTremoloBar($noteEffect);
    }
    if (($flags1 & 0x40) != 0)
    {
      $strokeDown = $this->readByte();
      $strokeUp = $this->readByte();
      if($strokeDown > 0 )
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_DOWN);
        $beat->getStroke()->setValue($this->factory('GuitarPro3Effects')->toStrokeValue($strokeDown));
      }
      else if($strokeUp > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_UP);
        $beat->getStroke()->setValue($this->factory('GuitarPro3Effects')->toStrokeValue($strokeUp));
      }
    }
    if (($flags2 & 0x02) != 0)
    {
      $this->readByte();
    }
  }

  /**
   * Reads EffectGrace
   * 
   * @param NoteEffect $effect
   */
  private function readGrace(NoteEffect $effect)
  {
    $fret = $this->readUnsignedByte();
    $dynamic = $this->readUnsignedByte();
    $transition = $this->readByte();
    $duration = $this->readUnsignedByte();
    $flags = $this->readUnsignedByte();

    $grace = new EffectGrace();
    $grace->setFret($fret);
    $grace->setDynamic((Velocities::MIN_VELOCITY 
      + (Velocities::VELOCITY_INCREMENT * $dynamic))
      - Velocities::VELOCITY_INCREMENT);
    $grace->setDuration($duration);
    $grace->setDead(($flags & 0x01) == 0);
    $grace->setOnBeat(($flags & 0x02) == 0);

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

    $effect->setGrace($grace);
  }

  /**
   * Reads lyrics informations
   * 
   * @return Lyric
   */
  private function readLyrics()
  {
    $lyric = new Lyric();
    $lyric->setFrom($this->readInt());
    $lyric->setLyrics($this->readStringInteger());

    for ($i = 0; $i < 4; $i++)
    {
      $this->readInt();
      $this->readStringInteger();
    }

    return $lyric;
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

    for ($i = 0; $i < $count; $i++) 
    {
      if($i > 0)
      {
        $this->skip();
      }

      $song->addMeasureHeader($this->factory('GuitarPro5MeasureHeader')->readMeasureHeader($i, $timeSignature));
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
      $this->readArtificialHarmonic($noteEffect);
    }

    if (($flags2 & 0x20) != 0)
    {
      $this->readTrill($noteEffect);
    }

    $noteEffect->setHammer((($flags1 & 0x02) != 0));
    $noteEffect->setLetRing((($flags1 & 0x08) != 0));
    $noteEffect->setVibrato((($flags2 & 0x40) != 0) || $noteEffect->isVibrato());
    $noteEffect->setPalmMute((($flags2 & 0x02) != 0));
    $noteEffect->setStaccato((($flags2 & 0x01) != 0));
  }

  /**
   * Reads setup informations
   * 
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
   * @param Song $song
   * @param int $count
   * @param array $channels array of channels
   * @param Lyric $lyric
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

  /**
   * Reads trill effect
   * 
   * @param NoteEffect $effect
   */
  private function readTrill(NoteEffect $effect)
  {
    $fret = $this->readByte();
    $period = $this->readByte();
    $trill = new EffectTrill();
    $trill->setFret($fret);
    if($period == 1)
    {
      $trill->getDuration()->setValue(Duration::SIXTEENTH);
      $effect->setTrill($trill);
    }
    else if($period == 2)
    {
      $trill->getDuration()->setValue(Duration::THIRTY_SECOND);
      $effect->setTrill($trill);
    }
    else if($period == 3)
    {
      $trill->getDuration()->setValue(Duration::SIXTY_FOURTH);
      $effect->setTrill($trill);
    }
  }
}
