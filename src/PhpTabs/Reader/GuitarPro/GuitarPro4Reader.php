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
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Note;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Song;
use PhpTabs\Model\Stroke;
use PhpTabs\Model\TabString;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Model\Track;
use PhpTabs\Model\Velocities;

class GuitarPro4Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v4.00', 'FICHIER GUITAR PRO v4.06', 'FICHIER GUITAR PRO L4.06');

  /**
   * @var boolean $tripletFeel
   * @var integer $keySignature
   */  
  private $tripletFeel, $keySignature;

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

    $this->song = new Song();

    $this->setTablature($this->song);

    $this->factory('GuitarPro3Informations')->readInformations($this->song);

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
    $lyric = $this->readLyrics();

    $tempoValue = $this->readInt();

    $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
    $this->skip(3);

    $this->readByte();

    $channels = $this->factory('GuitarProChannels')->readChannels();

    $measures = $this->readInt();
    $tracks = $this->readInt();

    $this->readMeasureHeaders($this->song, $measures);
    $this->readTracks($this->song, $tracks, $channels, $lyric, $lyricTrack);

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

  public function getKeySignature()
  {
    return $this->keySignature;
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
    $noteEffect->setVibrato((($flags1  & 0x02) != 0));
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
   * Reads Chord informations
   * 
   * @param integer $strings
   * @param Beat $beat
   */
  public function readChord($strings, Beat $beat)
  {
    $chord = new Chord($strings);
    if (($this->readUnsignedByte() & 0x01) == 0)
    {
      $chord->setName($this->readStringByteSizeOfInteger());
      $chord->setFirstFret($this->readInt());
      if($chord->getFirstFret() != 0)
      {
        for ($i = 0; $i < 6; $i++)
        {
          $fret = $this->readInt();
          if($i < $chord->countStrings())
          {
            $chord->addFretValue($i, $fret);
          }
        }
      }
    }
    else
    {
      $this->skip(16);
      $chord->setName($this->readStringByte(21));
      $this->skip(4);
      $chord->setFirstFret($this->readInt());
      for ($i = 0; $i < 7; $i++)
      {
        $fret = $this->readInt();
        if($i < $chord->countStrings())
        {
          $chord->addFretValue($i, $fret);
        }
      }
    
      $this->skip(32);
    }

    if($chord->countNotes() > 0)
    {
      $beat->setChord($chord);
    }
  }

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
   * Reads a mesure header
   * 
   * @param integer $number
   * @param Song $song
   * @param TimeSignature $timeSignature
   * @param integer $tempoValue
   * 
   * @return MeasureHeader
   */
  private function readMeasureHeader($number, Song $song, TimeSignature $timeSignature, $tempoValue = 120)
  {
    $flags = $this->readUnsignedByte();
    $header = new MeasureHeader();
    $header->setNumber($number);
    $header->setStart(0);
    $header->getTempo()->setValue($tempoValue);
    $header->setTripletFeel($this->tripletFeel);
    $header->setRepeatOpen( (($flags & 0x04) != 0) );

    if (($flags & 0x01) != 0)
    {
      $timeSignature->setNumerator($this->readByte());
    }

    if (($flags & 0x02) != 0)
    {
      $timeSignature->getDenominator()->setValue($this->readByte());
    }

    $header->getTimeSignature()->copyFrom($timeSignature);
    if (($flags & 0x08) != 0)
    {
      $header->setRepeatClose($this->readByte());
    }

    if (($flags & 0x10) != 0)
    {
      $header->setRepeatAlternative($this->factory('GuitarPro3RepeatAlternative')->parseRepeatAlternative($song, $number));
    }

    if (($flags & 0x20) != 0)
    {
      $header->setMarker($this->factory('GuitarProMarker')->readMarker($number));
    }

    if (($flags & 0x40) != 0)
    {
      $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
      $this->skip(1);
    }

    return $header;
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
      $song->addMeasureHeader($this->readMeasureHeader(($i + 1), $song, $timeSignature));
    }
  }

  /**
   * Reads mix change informations
   * 
   * @param Tempo $tempo
   */
  public function readMixChange(Tempo $tempo)
  {
    $this->readByte(); //instrument
    $volume = $this->readByte();
    $pan = $this->readByte();
    $chorus = $this->readByte();
    $reverb = $this->readByte();
    $phaser = $this->readByte();
    $tremolo = $this->readByte();
    $tempoValue = $this->readInt();
    if($volume >= 0)
    {
      $this->readByte();
    }
    if($pan >= 0)
    {
      $this->readByte();
    }
    if($chorus >= 0)
    {
      $this->readByte();
    }
    if($reverb >= 0)
    {
      $this->readByte();
    }
    if($phaser >= 0)
    {
      $this->readByte();
    }
    if($tremolo >= 0)
    {
      $this->readByte();
    }
    if($tempoValue >= 0)
    {
      $tempo->setValue($tempoValue);
      $this->readByte();
    }
    
    $this->readByte();
  }

  /**
   * Reads a note
   * 
   * @param TabString $string
   * @param track $track
   * @param NoteEffect $effect
   * @return Note
   */
  public function readNote(TabString $string, Track $track, NoteEffect $effect)
  {
    $flags = $this->readUnsignedByte();
    $note = new Note();
    $note->setString($string->getNumber());
    $note->setEffect($effect);
    $note->getEffect()->setAccentuatedNote((($flags & 0x40) != 0));
    $note->getEffect()->setGhostNote((($flags & 0x04) != 0));
    if (($flags & 0x20) != 0)
    {
      $noteType = $this->readUnsignedByte();
      $note->setTiedNote($noteType == 0x02);
      $note->getEffect()->setDeadNote($noteType == 0x03);
    }
    if (($flags & 0x01) != 0)
    {
      $this->skip(2);
    }
    if (($flags & 0x10) != 0)
    {
      $note->setVelocity( (Velocities::MIN_VELOCITY + (Velocities::VELOCITY_INCREMENT * $this->readByte())) - Velocities::VELOCITY_INCREMENT);
    }
    if (($flags & 0x20) != 0)
    {
      $fret = $this->readByte();

      $value = $note->isTiedNote() ? 
        $this->factory('GuitarPro3TiedNote')->getTiedNoteValue($string->getNumber(), $track)
        : $fret;

      $note->setValue($value >= 0 && $value < 100 ? $value : 0);
    }
    if (($flags & 0x80) != 0)
    {
      $this->skip(2);
    }
    if (($flags & 0x08) != 0)
    {
      $this->readNoteEffects($note->getEffect());
    }

    return $note;
  }

  /**
   * Reads NoteEffect
   * 
   * @param NoteEffect $noteEffect
   */
  private function readNoteEffects(NoteEffect $noteEffect)
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
