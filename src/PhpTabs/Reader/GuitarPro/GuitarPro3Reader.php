<?php

namespace PhpTabs\Reader\GuitarPro;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Channel;
use PhpTabs\Model\ChannelParameter;
use PhpTabs\Model\Chord;
use PhpTabs\Model\Color;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectBend;
use PhpTabs\Model\EffectGrace;
use PhpTabs\Model\EffectHarmonic;
use PhpTabs\Model\EffectTremoloBar;
use PhpTabs\Model\Lyric;
use PhpTabs\Model\Marker;
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Note;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Song;
use PhpTabs\Model\Stroke;
use PhpTabs\Model\TabString;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\Text;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Model\Track;
use PhpTabs\Model\Velocities;

class GuitarPro3Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v3.00');

  /**
   * @var boolean $tripletFeel
   * @var integer $keySignature
   */  
  private $tripletFeel, $keySignature;

  /**
   * Constructor
   * @param File $file input file to read
   * @return void
   */
  public function __construct(File $file)
  {
    parent::__construct($file);

    $this->readVersion();

    if (!$this->isSupportedVersion($this->getVersion()))
    {
      $this->closeStream();

      throw new \Exception(sprintf('Version "%s" is not supported', $this->getVersion()));
    }

    $this->song = new Song();

    $this->setTablature($this->song);

    $this->readInformations($this->song);

    $this->tripletFeel = $this->readBoolean()
      ? MeasureHeader::TRIPLET_FEEL_EIGHTH
      : MeasureHeader::TRIPLET_FEEL_NONE;

    $tempoValue = $this->readInt();

    $this->keySignature = $this->readKeySignature();
    $this->skip(3);

    # Meta only
    if(Config::get('type') == 'meta')
    {
      $this->closeStream();

      return;
    }

    $channels = $this->readChannels();

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

    $this->readMeasures($this->song, $measures, $tracks, $tempoValue);

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
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * @param Track $track
   * @return integer Clef of $track
   */
  private function getClef(Track $track)
  {
    if(!$this->isPercussionChannel($track->getSong(), $track->getChannelId()))
    {
      $strings = $track->getStrings();

      foreach($strings as $string)
      {
        if($string->getValue() <= 34)
        {
          return Measure::CLEF_BASS;
        }
      }
    }

    return Measure::CLEF_TREBLE;
  }

  /**
   * @param TabString $string String on which note has started
   * @param Track $track
   * @return integer tied note value
   */
  private function getTiedNoteValue($string, Track $track)
  {
    $measureCount = $track->countMeasures();

    if ($measureCount > 0)
    {
      for ($m = $measureCount - 1; $m >= 0; $m--)
      {
        $measure = $track->getMeasure($m);

        for ($b = $measure->countBeats() - 1; $b >= 0; $b--)
        {
          $beat = $measure->getBeat($b);
          $voice = $beat->getVoice(0);  

          for ($n = 0; $n < $voice->countNotes(); $n++)
          {
            $note = $voice->getNote($n);

            if ($note->getString() == $string)
            {
              return $note->getValue();
            }
          }
        }
      }
    }

    return -1;
  }

  /**
   * Manage repeat alternative
   * 
   * @param Song $song
   * @param integer $measure
   * @param integer $value
   * @return integer Number of repeat alternatives
   */
  private function parseRepeatAlternative(Song $song, $measure, $value)
  {
    $repeatAlternative = 0;
    $existentAlternatives = 0;
    $headers = $song->getMeasureHeaders();
    foreach($headers as $header)
    {
      if($header->getNumber() == $measure)
      {
        break;
      }
      if($header->isRepeatOpen())
      {
        $existentAlternatives = 0;
      }

      $existentAlternatives |= $header->getRepeatAlternative();
    }

    for($i = 0; $i < 8; $i++)
    {
      if($value > $i && ($existentAlternatives & (1 << $i)) == 0)
      {
        $repeatAlternative |= (1 << $i);
      }
    }

    return $repeatAlternative;
  }

  /**
   * Reads some Beat informations
   * 
   * @param integer $start
   * @param Measure $measure
   * @param Track $track
   * @param Tempo $tempo
   * 
   * @return integer $time duration time
   */
  private function readBeat($start, Measure $measure, Track $track, Tempo $tempo)
  {
    $flags = $this->readUnsignedByte();

    if(($flags & 0x40) != 0)
    {
      $this->readUnsignedByte();
    }

    $beat = new Beat();
    $voice = $beat->getVoice(0);
    $duration = $this->readDuration($flags);
    $effect = new NoteEffect();

    if (($flags & 0x02) != 0)
    {
      $this->readChord($track->countStrings(), $beat);
    }
    if (($flags & 0x04) != 0) 
    {
      $this->readText($beat);
    }
    if (($flags & 0x08) != 0)
    {
      $this->readBeatEffects($beat, $effect);
    }
    if (($flags & 0x10) != 0)
    {
      $this->readMixChange($tempo);
    }

    $stringFlags = $this->readUnsignedByte();

    for ($i = 6; $i >= 0; $i--)
    {
      if (($stringFlags & (1 << $i)) != 0 && (6 - $i) < $track->countStrings())
      {
        $string = clone $track->getString( (6 - $i) + 1 );
        $note = $this->readNote($string, $track, clone $effect);
        $voice->addNote($note);
      }
    }

    $beat->setStart($start);
    $voice->setEmpty(false);
    $voice->getDuration()->copyFrom($duration);
    $measure->addBeat($beat);

    return $duration->getTime();
  }

  /**
   * Reads some NoteEffect informations
   * 
   * @param Beat $beat
   * @param NoteEffect $effect
   * @return void
   */
  private function readBeatEffects(Beat $beat, NoteEffect $effect)
  {
    $flags = $this->readUnsignedByte();
    $effect->setVibrato((($flags & 0x01) != 0) || (($flags & 0x02) != 0));
    $effect->setFadeIn((($flags & 0x10) != 0));
    if (($flags & 0x20) != 0)
    {
      $type = $this->readUnsignedByte();
      if ($type == 0)
      {
        $this->readTremoloBar($effect);
      }
      else
      {
        $effect->setTapping($type == 1);
        $effect->setSlapping($type == 2);
        $effect->setPopping($type == 3);
        $this->readInt();
      }
    }
    if (($flags & 0x40) != 0)
    {
      $strokeDown = $this->readByte();
      $strokeUp = $this->readByte();
      if($strokeDown > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_DOWN );
        $beat->getStroke()->setValue($this->toStrokeValue($strokeDown));
      }
      else if($strokeUp > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_UP);
        $beat->getStroke()->setValue($this->toStrokeValue($strokeUp));
      }
    }
    if (($flags & 0x04) != 0)
    {
      $harmonic = new EffectHarmonic();
      $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      $effect->setHarmonic($harmonic);
    }
    if (($flags & 0x08) != 0)
    {
      $harmonic = new EffectHarmonic();
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $harmonic->setData(0);
      $effect->setHarmonic($harmonic);
    }
  }

  /**
   * Reads BendEffect informations
   *
   * @param NoteEffect $effect
   * @return void
   */
  private function readBend(NoteEffect $effect)
  {
    $bend = new EffectBend();
    $this->skip(5);
    $points = $this->readInt();
    for ($i = 0; $i < $points; $i++)
    {
      $bendPosition = $this->readInt();
      $bendValue = $this->readInt();
      $this->readByte(); //vibrato

      $pointPosition = round($bendPosition * EffectBend::MAX_POSITION_LENGTH / GuitarProReaderInterface::GP_BEND_POSITION);
      $pointValue = round($bendValue * EffectBend::SEMITONE_LENGTH / GuitarProReaderInterface::GP_BEND_SEMITONE);
      $bend->addPoint($pointPosition, $pointValue);
    }
    if(count($bend->getPoints()))
    {
      $effect->setBend($bend);
    }
  }

  /**
   * Reads Channel informations
   * 
   * @param Song $song
   * @param Track $track
   * @param array $channels
   * 
   * @return void
   */
  private function readChannel(Song $song, Track $track, $channels)
  {
    $gChannel1 = $this->readInt() - 1;
    $gChannel2 = $this->readInt() - 1;

    if($gChannel1 >= 0 && $gChannel1 < count($channels))
    {
      $channel = new Channel();
      $gChannel1Param = new ChannelParameter();
      $gChannel2Param = new ChannelParameter();

      $gChannel1Param->setKey("channel-1");
      $gChannel1Param->setValue("$gChannel1");
      $gChannel2Param->setKey("channel-2");
      $gChannel2Param->setValue($gChannel1 != 9
        ? "$gChannel2" : "$gChannel1");

      $channel->copyFrom($channels[$gChannel1]);

      for($i = 0; $i < $song->countChannels(); $i++)
      {
        $channelAux = $song->getChannel($i);
        for($n = 0; $n < $channelAux->countParameters(); $n++)
        {
          $channelParameter = $channelAux->getParameter($n);
          if($channelParameter->getKey() == "$gChannel1")
          {
            if("$gChannel1" == $channelParameter->getValue())
            {
              $channel->setChannelId($channelAux->getChannelId());
            }
          }
        }
      }
      if($channel->getChannelId() <= 0)
      {
        $channel->setChannelId($song->countChannels() + 1);
        $channel->setName($this->createChannelNameFromProgram($song, $channel));
        $channel->addParameter($gChannel1Param);
        $channel->addParameter($gChannel2Param);
        $song->addChannel($channel);
      }

      $track->setChannelId($channel->getChannelId());
    }
  }

  /**
   * Reads channels informations
   * 
   * @return array $channels
   */
  private function readChannels()
  {
    $channels = array();

    for ($i=0; $i<64; $i++)
    {
      $channel = new Channel();
      $channel->setProgram($this->readInt());
      $channel->setVolume($this->toChannelShort($this->readByte()));
      $channel->setBalance($this->toChannelShort($this->readByte()));
      $channel->setChorus($this->toChannelShort($this->readByte()));
      $channel->setReverb($this->toChannelShort($this->readByte()));
      $channel->setPhaser($this->toChannelShort($this->readByte()));
      $channel->setTremolo($this->toChannelShort($this->readByte()));
      $channel->setBank($i == 9
        ? Channel::DEFAULT_PERCUSSION_BANK : Channel::DEFAULT_BANK);

      if ($channel->getProgram() < 0)
      {
        $channel->setProgram(0);
      }

      $channels[] = $channel;

      $this->skip(2);
    }

    return $channels;
  }

  /**
   * Reads color informations
   * 
   * @param Color $color
   * @return void
   */
  private function readColor(Color $color)
  {
    $color->setR($this->readUnsignedByte());
    $color->setG($this->readUnsignedByte());
    $color->setB($this->readUnsignedByte());
    $this->skip();
  }

  /**
   * Read Chord informations
   * 
   * @param integer $strings
   * @param Beat $beat
   * @return void
   */
  private function readChord($strings, $beat)
  {
    $chord = new Chord($strings);
    $header = $this->readUnsignedByte();
    if (($header & 0x01) == 0)
    {
      $chord->setName($this->readStringByteSizeOfInteger());
      $chord->setFirstFret($this->readInt());
      if ($chord->getFirstFret() != 0)
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
      $this->skip(25);
      $chord->setName($this->readStringByte(34));
      $chord->setFirstFret($this->readInt());
      for ($i = 0; $i < 6; $i++)
      {
        $fret = $this->readInt();
        if($i < $chord->countStrings())
        {
          $chord->addFretValue($i, $fret);
        }
      }
      $this->skip(36);
    }
    if($chord->countNotes() > 0)
    {
      $beat->setChord($chord);
    }
  }

  /**
   * Reads Duration
   *
   * @param byte $flags unsigned bytes
   * @return Duration
   */
  private function readDuration($flags)
  {
    $duration = new Duration();
    $duration->setValue(pow( 2 , ($this->readByte() + 4) ) / 4);
    $duration->setDotted(($flags & 0x01) != 0);
    if (($flags & 0x20) != 0)
    {
      $divisionType = $this->readInt();
      switch ($divisionType)
      {
        case 3:
          $duration->getDivision()->setEnters(3);
          $duration->getDivision()->setTimes(2);
          break;
        case 5:
          $duration->getDivision()->setEnters(5);
          $duration->getDivision()->setTimes(4);
          break;
        case 6:
          $duration->getDivision()->setEnters(6);
          $duration->getDivision()->setTimes(4);
          break;
        case 7:
          $duration->getDivision()->setEnters(7);
          $duration->getDivision()->setTimes(4);
          break;
        case 9:
          $duration->getDivision()->setEnters(9);
          $duration->getDivision()->setTimes(8);
          break;
        case 10:
          $duration->getDivision()->setEnters(10);
          $duration->getDivision()->setTimes(8);
          break;
        case 11:
          $duration->getDivision()->setEnters(11);
          $duration->getDivision()->setTimes(8);
          break;
        case 12:
          $duration->getDivision()->setEnters(12);
          $duration->getDivision()->setTimes(8);
          break;
      }
    }

    return $duration;
  }

  /**
   * Reads GraceEffect
   * 
   * @param NoteEffect $effect
   * @return void
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
   * Reads meta informations about tablature
   * 
   * @param Song $song
   * @return void
   */
  private function readInformations(Song $song)
  {
    $song->setName($this->readStringByteSizeOfInteger());
    $song->setTranscriber($this->readStringByteSizeOfInteger());
    $song->setArtist($this->readStringByteSizeOfInteger());
    $song->setAlbum($this->readStringByteSizeOfInteger());
    $song->setAuthor($this->readStringByteSizeOfInteger());
    $song->setCopyright($this->readStringByteSizeOfInteger());
    $song->setWriter($this->readStringByteSizeOfInteger());

    $song->setTranscriber($this->readStringByteSizeOfInteger());
    $comments = $this->readInt();
    for ($i=0; $i<$comments; $i++)
    {
      $song->setComments($song->getComments() . $this->readStringByteSizeOfInteger());
    }
  }

  /**
   * Reads the key signature
   * 
   * @return integer Key signature 0: C 1: G, -1: F
   */
  private function readKeySignature()
  {
    $keySignature = $this->readByte();

    if ($keySignature < 0)
    {
      $keySignature = 7 - $keySignature; // -1 to 8 [...]
    }

    return $keySignature;
  }

  /**
   * Reads measure marker
   * 
   * @param integer $measure
   * @return Marker
   */
  private function readMarker($measure)
  {
    $marker = new Marker();
    $marker->setMeasure($measure);
    $marker->setTitle($this->readStringByteSizeOfInteger());
    $this->readColor($marker->getColor());
    return $marker;
  }

  /**
   * Reads a Measure
   * 
   * @param Measure $measure
   * @param Track $track
   * @param Tempo $tempo
   * @return void
   */
  private function readMeasure(Measure $measure, Track $track, Tempo $tempo)
  {
    $nextNoteStart = intval($measure->getStart());
    $numberOfBeats = $this->readInt();

    for ($i = 0; $i < $numberOfBeats; $i++)
    {
      $nextNoteStart += $this->readBeat($nextNoteStart, $measure, $track, $tempo);
      if($i>256)
      {
        $message = sprintf('%s: Too much beats (%s) in measure %s of Track[%s]'
          , __METHOD__, $numberOfBeats, $measure->getNumber(), $track->getName());
        throw new \Exception($message);
      }
    }

    $measure->setClef( $this->getClef($track) );
    $measure->setKeySignature($this->keySignature);
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
      $header->setRepeatAlternative($this->parseRepeatAlternative($song, $number, $this->readUnsignedByte()));
    }

    if (($flags & 0x20) != 0)
    {
      $header->setMarker($this->readMarker($number));
    }

    if (($flags & 0x40) != 0)
    {
      $this->keySignature = $this->readKeySignature();
      $this->skip(1);
    }

    return $header;
  }

  /**
   * Loops on mesure headers to read
   * 
   * @param Song $song
   * @param integer $count
   * @return void
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
   * Loops on mesures to read
   * 
   * @param Song $song
   * @param integer $measures
   * @param integer $tracks
   * @param integer $tempoValue
   * @return void
   */
  private function readMeasures(Song $song, $measures, $tracks, $tempoValue)
  {
    $tempo = new Tempo();
    $tempo->setValue($tempoValue);
    $start = Duration::QUARTER_TIME;
    for ($i = 0; $i < $measures; $i++)
    {
      $header = $song->getMeasureHeader($i);
      $header->setStart($start);
      for ($j = 0; $j < $tracks; $j++)
      {
        $track = $song->getTrack($j);
        $measure = new Measure($header);

        $track->addMeasure($measure);
        $this->readMeasure($measure, $track, $tempo);
      }

      $header->getTempo()->copyFrom($tempo);
      $start += $header->getLength();
    }
  }

  /**
   * Reads mix change informations
   * 
   * @param Tempo $tempo
   * @return void
   */
  private function readMixChange(Tempo $tempo)
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
  }

  /**
   * Reads a note
   * 
   * @param TabString $string
   * @param track $track
   * @param NoteEffect $effect
   * @return Note
   */
  private function readNote(TabString $string, Track $track, NoteEffect $effect)
  {
    $flags = $this->readUnsignedByte();
    $note = new Note();
    $note->setString($string->getNumber());
    $note->setEffect($effect);
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
      $value = $note->isTiedNote()
        ? $this->getTiedNoteValue($string->getNumber(), $track) : $fret;
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
   * @return void
   */
  private function readNoteEffects(NoteEffect $effect)
  {
    $flags = $this->readUnsignedByte();
    $effect->setHammer( (($flags & 0x02) != 0) );
    $effect->setSlide( (($flags & 0x04) != 0) );
    $effect->setLetRing((($flags & 0x08) != 0));
    if (($flags & 0x01) != 0)
    {
      $this->readBend($effect);
    }
    if (($flags & 0x10) != 0)
    {
      $this->readGrace($effect);
    }
  }

  /**
   * Reads some text
   * 
   * @param Beat $beat
   * @return void
   */
  private function readText(Beat $beat)
  {
    $text = new Text();
    $text->setValue($this->readStringByteSizeOfInteger());
    $beat->setText($text);
  }

  /**
   * Reads Track informations
   * @param Song $song
   * @param integer $number
   * @param array $channels an array of Channel objects
   * @return Track
   */
  private function readTrack(Song $song, $number, $channels)
  {
    $track = new Track();
    $track->setSong($song);
    $track->setNumber($number);
    $this->readUnsignedByte();
    $track->setName($this->readStringByte(40));
    $stringCount = $this->readInt();
    for ($i = 0; $i < 7; $i++)
    {
      $tuning = $this->readInt();
      if ($stringCount > $i)
      {
        $string = new TabString();
        $string->setNumber($i + 1);
        $string->setValue($tuning);
        $track->addString($string);
      }
    }
    $this->readInt();
    $this->readChannel($song, $track, $channels);
    $this->readInt();
    $track->setOffset($this->readInt());
    $this->readColor($track->getColor());

    return $track;
  }

  /**
   * Loops on tracks to read
   * 
   * @param Song $song
   * @param int $count
   * @param array $channels Current array of channels
   * @return void
   */
  private function readTracks(Song $song, $count, array $channels)
  {
    for ($number = 1; $number <= $count; $number++)
    {
      $song->addTrack($this->readTrack($song, $number, $channels));
    }
  }

  /**
   * Reads tremolo bar
   * 
   * @param NoteEffect $noteEffect
   * @return void
   */
  private function readTremoloBar(NoteEffect $noteEffect)
  {
    $value = $this->readInt();
    $effect = new EffectTremoloBar();
    $effect->addPoint(0, 0);
    $effect->addPoint(round(EffectTremoloBar::MAX_POSITION_LENGTH / 2)
      , round( -($value / (GuitarProReaderInterface::GP_BEND_SEMITONE * 2))));
    $effect->addPoint(EffectTremoloBar::MAX_POSITION_LENGTH, 0);
    $noteEffect->setTremoloBar($effect);
  }

	/**
   * Get stroke value
   * 
   * @param integer $value
   * @return integer stroke value
   */
  private function toStrokeValue($value)
  {
    if($value == 1 || $value == 2)
    {
      return Duration::SIXTY_FOURTH;
    }
    if($value == 3)
    {
      return Duration::THIRTY_SECOND;
    }
    if($value == 4)
    {
      return Duration::SIXTEENTH;
    }
    if($value == 5)
    {
      return Duration::EIGHTH;
    }
    if($value == 6)
    {
      return Duration::QUARTER;
    }

    return Duration::SIXTY_FOURTH;
  }
}
