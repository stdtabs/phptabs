<?php

namespace PhpTabs\Reader\GuitarPro;

use Exception;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Channel;
use PhpTabs\Model\ChannelParameter;
use PhpTabs\Model\Chord;
use PhpTabs\Model\Duration;
use PhpTabs\Model\Lyric;
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Note;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Song;
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

    $this->readInformations($this->song);

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
    $this->tablature->setFormat('gp3');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

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
      $this->factory('GuitarPro3Effects')->readBeatEffects($beat, $effect);
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
   * Reads Channel informations
   * 
   * @param Song $song
   * @param Track $track
   * @param array $channels
   * 
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
   * Read Chord informations
   * 
   * @param integer $strings
   * @param Beat $beat
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
   * Reads meta informations about tablature
   * 
   * @param Song $song
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
   * Reads a Measure
   * 
   * @param Measure $measure
   * @param Track $track
   * @param Tempo $tempo
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
        throw new Exception($message);
      }
    }

    $measure->setClef( $this->factory('GuitarProClef')->getClef($track) );
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
   * Loops on mesures to read
   * 
   * @param Song $song
   * @param integer $measures
   * @param integer $tracks
   * @param integer $tempoValue
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
        ? $this->factory('GuitarPro3TiedNote')->getTiedNoteValue($string->getNumber(), $track)
        : $fret;

      $note->setValue($value >= 0 && $value < 100 ? $value : 0);
    }
    if (($flags & 0x80) != 0)
    {
      $this->skip(2);
    }
    if (($flags & 0x08) != 0)
    {
      $this->factory('GuitarPro3Effects')->readNoteEffects($note->getEffect());
    }

    return $note;
  }

  /**
   * Reads some text
   * 
   * @param Beat $beat
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
    $this->factory('GuitarProColor')->readColor($track->getColor());

    return $track;
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
      $song->addTrack($this->readTrack($song, $number, $channels));
    }
  }
}
