<?php

namespace PhpTabs\Reader\GuitarPro;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\ReaderInterface;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Channel;
use PhpTabs\Model\ChannelNames;
use PhpTabs\Model\ChannelParameter;
use PhpTabs\Model\Color;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectBend;
use PhpTabs\Model\EffectGrace;
use PhpTabs\Model\Lyric;
use PhpTabs\Model\Marker;
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Note;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Song;
use PhpTabs\Model\TabString as TabString;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\Text;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Model\Track;
use PhpTabs\Model\Velocities;



class GuitarPro3Reader extends GuitarProReaderBase implements ReaderInterface, GuitarProReaderInterface
{
  private static $supportedVersions = array('FICHIER GUITAR PRO v3.00');

  private $tripletFeel;
  private $keySignature;


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

    # Only analyse meta informations
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

    # Only analyse meta informations and channels
    if(Config::get('type') == 'channels')
    {
      $this->closeStream();

      return;
    }


    $this->readMeasures($this->song, $measures, $tracks, $tempoValue);

    $this->closeStream();
  }

  public function getSupportedVersions()
  {
    return GuitarPro3Reader::$supportedVersions;
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

  private function getClef(Track $track)
  {
    if(!$this->isPercussionChannel($track->getSong(), $track->getChannelId()))
    {
      $strings = $track->getStrings();
      foreach($strings as $k => $string)
      {
        if($string->getValue() <= 34)
        {
          return Measure::CLEF_BASS;
        }
      }
    }

    return Measure::CLEF_TREBLE;
  }

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

  private function isPercussionChannel(Song $song, $channelId)
  {
    $channels = $song->getChannels();

    foreach($channels as $k => $channel)
    {
      if($channel->getChannelId() == $channelId)
      {
        return $channel->isPercussionChannel();
      }
    }

    return false;
  }

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
      $this->readChord($track->stringCount(), $beat);
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
      if (($stringFlags & (1 << $i)) != 0 && (6 - $i) < $track->stringCount())
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

  private function readChannel(Song $song, Track $track, $channels)
  {
    $gmChannel1 = $this->readInt() - 1;
    $gmChannel2 = $this->readInt() - 1;

    if($gmChannel1 >= 0 && $gmChannel1 < count($channels))
    {
      $channel = new Channel();
      $gmChannel1Param = new ChannelParameter();
      $gmChannel2Param = new ChannelParameter();

      $gmChannel1Param->setKey("gm-channel-1");
      $gmChannel1Param->setValue("$gmChannel1");
      $gmChannel2Param->setKey("gm-channel-2");
      $gmChannel2Param->setValue($gmChannel1 != 9
        ? "$gmChannel2" : "$gmChannel1");

      $channel->copyFrom($channels[$gmChannel1]);

      //------------------------------------------//
      for($i = 0; $i < $song->countChannels(); $i++)
      {
        $channelAux = $song->getChannel($i);
        for($n = 0; $n < $channelAux->countParameters(); $n++)
        {
          $channelParameter = $channelAux->getParameter($n);
          if($channelParameter->getKey() == "$gmChannel1")
          {
            if("$gmChannel1" == $channelParameter->getValue())
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
        $channel->addParameter($gmChannel1Param);
        $channel->addParameter($gmChannel2Param);
        $song->addChannel($channel);
      }

      $track->setChannelId($channel->getChannelId());
    }
  }

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

  private function readColor(Color $color)
  {
    $color->setR($this->readUnsignedByte());
    $color->setG($this->readUnsignedByte());
    $color->setB($this->readUnsignedByte());
    $this->skip();
  }

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
   * 0: C 1: G, -1: F
   * @return int Key signature
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
   * @param int $measure
   */
  private function readMarker($measure)
  {
    $marker = new Marker();
    $marker->setMeasure($measure);
    $marker->setTitle($this->readStringByteSizeOfInteger());
    $this->readColor($marker->getColor());
    return $marker;
  }

  private function readMeasure(Measure $measure, Track $track, Tempo $tempo)
  {
    $nextNoteStart = (double)($measure->getStart());
    $numberOfBeats = $this->readInt();

    for ($i = 0; $i < $numberOfBeats; $i++)
    {
      $nextNoteStart += $this->readBeat($nextNoteStart, $measure, $track, $tempo);
      if($i>100) throw new \Exception(__METHOD__  . ": Too much beats ($numberOfBeats) in measure " . $measure->getNumber());
    }
    $measure->setClef( $this->getClef($track) );
    $measure->setKeySignature($this->keySignature);
  }

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


  private function readMeasureHeaders(Song $song, $count)
  {
    $timeSignature = new TimeSignature();

    for ($i=0; $i<$count; $i++) 
    {
      $song->addMeasureHeader($this->readMeasureHeader(($i + 1), $song, $timeSignature));
    }
  }


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
      $value = $note->isTiedNote() ? $this->getTiedNoteValue($string->getNumber(), $track) : $fret;
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

  private function readText(Beat $beat)
  {
    $text = new Text();
    $text->setValue($this->readStringByteSizeOfInteger());
    $beat->setText($text);
  }

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
   * @param Song $song
   * @param int $count
   * @param array $channels Current array of channels
   */
  private function readTracks(Song $song, $count, array $channels)
  {
    for ($number = 1; $number <= $count; $number++)
    {
      $song->addTrack($this->readTrack($song, $number, $channels));
    }
  }

  /**
   * @return Short int between 0 and 32767
   */
  private function toChannelShort($b)
  {
    $value = ($b * 8) - 1;

    if($value>32767)
      $value = 0; #workaround @todo FIXME

    return max($value, 0);
  }


  /********************************************************************/
  // EXTRA Should be implemented differently: maybe in SongManager
  // @todo move all functions below
  /********************************************************************/

  public function findChannelsByName($song, $name)
  {
    $channels = $song->getChannels();

    foreach($channels as $v)
    {
      if($v->getName() == $name)
        return true;
    }

    return false;
  }

  public function createChannelName(Song $song, $prefix)
  {
    $number = 0;
    $unusedName = null;

    while( $unusedName == null )
    {
      $number ++;
      $name = $prefix . " " . $number;
      if(!$this->findChannelsByName($song, $name))
      {
        $unusedName = $name;
      }
    }

    return $unusedName;
  }

  public function createDefaultChannelName(Song $song)
  {
    return $this->createChannelName($song, "Unnamed");
  }


  public function createChannelNameFromProgram(Song $song, $channel)
  {
    $names = ChannelNames::$DEFAULT_NAMES;

    if($channel->getProgram() >= 0 && isset($names[$channel->getProgram()]))
    {
      return $this->createChannelName($song, ChannelNames::$DEFAULT_NAMES[$channel->getProgram()]);
    }

    return $this->createDefaultChannelName($song);
  }

  /********************************************************************/
  // EXTRA Should be implemented differently: maybe in SongManager or Song
  // @todo move all functions below
  /********************************************************************/
}
