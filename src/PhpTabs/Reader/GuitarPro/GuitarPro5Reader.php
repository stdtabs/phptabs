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
use PhpTabs\Model\EffectTremoloPicking;
use PhpTabs\Model\EffectTrill;
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

class GuitarPro5Reader extends GuitarProReaderBase
{
  /** @var array $supportedVersions */
  private static $supportedVersions = array('FICHIER GUITAR PRO v5.00', 'FICHIER GUITAR PRO v5.10');

  /**
   * @var integer $keySignature
   */  
  private $keySignature;

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

      throw new \Exception(sprintf('Version "%s" is not supported', $this->getVersion()));
    }

    $this->song = new Song();

    $this->setTablature($this->song);

    $this->readInformations($this->song);

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

    $this->keySignature = $this->readKeySignature();
    $this->skip(3);

    $this->readByte();

    $channels = $this->readChannels();

    $this->skip(42);

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
    $this->tablature->setFormat('gp5');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * Creates a new Beat if necessary
   * 
   * @param Measure $mesure
   * @param integer $start
   * @return Beat
   */
  private function getBeat(Measure $measure, $start)
  {
    $count = $measure->countBeats();
    for($i = 0; $i < $count; $i++)
    {
      $beat = $measure->getBeat($i);
      if($beat->getStart() == $start)
      {
        return $beat;
      }
    }
    $beat = new Beat();
    $beat->setStart($start);
    $measure->addBeat($beat);

    return $beat;
  }

  /**
   * Gets Track key
   *
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
   * Gets tied note value
   *
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
          
          for($v = 0; $v < $beat->countVoices(); $v++)
          {
            $voice = $beat->getVoice($v);  

            if(!$voice->isEmpty())
            {
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
      }
    }

    return -1;
  }

  /**
   * Reads an artificial harmonic
   * 
   * @param NoteEffect $effect
   * @return void
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
   * Reads some Beat informations
   * 
   * @param integer $start
   * @param Measure $measure
   * @param Track $track
   * @param Tempo $tempo
   * @param integer $voiceIndex
   * 
   * @return integer $time duration time
   */
  private function readBeat($start, Measure $measure, Track $track, Tempo $tempo, $voiceIndex)
  {
    $flags = $this->readUnsignedByte();

    $beat = $this->getBeat($measure, $start);
    $voice = $beat->getVoice($voiceIndex);

    if(($flags & 0x40) != 0)
    {
      $beatType = $this->readUnsignedByte();
      $voice->setEmpty($beatType & 0x02 == 0);
    }

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

      $voice->getDuration()->copyFrom($duration);
    }

    $this->skip();

    if($this->readByte() & 0x08 != 0)
    {
      $this->skip();
    }

    return !$voice->isEmpty() ? $duration->getTime() : 0;
  }

  /**
   * Reads some NoteEffect informations
   * 
   * @param Beat $beat
   * @param NoteEffect $effect
   * @return void
   */
  private function readBeatEffects(Beat $beat, NoteEffect $noteEffect)
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
      $this->readTremoloBar($noteEffect);
    }
    if (($flags1 & 0x40) != 0)
    {
      $strokeDown = $this->readByte();
      $strokeUp = $this->readByte();
      if($strokeDown > 0 )
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_DOWN);
        $beat->getStroke()->setValue($this->toStrokeValue($strokeDown));
      }
      else if($strokeUp > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_UP);
        $beat->getStroke()->setValue($this->toStrokeValue($strokeUp));
      }
    }
    if (($flags2 & 0x02) != 0)
    {
      $this->readByte();
    }
  }

  /**
   * Reads EffectBend informations
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
      $this->readByte();

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
   * Reads Chord informations
   * 
   * @param integer $strings
   * @param Beat $beat
   * @return void
   */
  private function readChord($strings,Beat $beat)
  {
    $chord = new Chord($strings);
    $this->skip(17);
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
    if($chord->countNotes() > 0)
    {
      $beat->setChord($chord);
    }
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
   * Reads EffectGrace
   * 
   * @param NoteEffect $effect
   * @return void
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
    $grace->setDead($flags & 0x01 == 0);
    $grace->setOnBeat($flags & 0x02 == 0);

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
   * Reads meta informations about tablature
   * 
   * @param Song $song
   * @return void
   */
  private function readInformations(Song $song)
  {
    $song->setName($this->readStringByteSizeOfInteger());
    $this->readStringByteSizeOfInteger();
    $song->setArtist($this->readStringByteSizeOfInteger());
    $song->setAlbum($this->readStringByteSizeOfInteger());
    $song->setAuthor($this->readStringByteSizeOfInteger());
    $this->readStringByteSizeOfInteger();
    $song->setCopyright($this->readStringByteSizeOfInteger());
    $song->setWriter($this->readStringByteSizeOfInteger());
    $this->readStringByteSizeOfInteger();
    $comments = $this->readInt();
    for ($i=0; $i<$comments; $i++)
    {
      $song->setComments($song->getComments() . $this->readStringByteSizeOfInteger());
    }
  }

  /**
   * Reads the key signature
   * 
   * 0: C 1: G, -1: F
   * @return integer Key signature
   */
  private function readKeySignature()
  {
    $keySignature = $this->readByte();

    if ($keySignature < 0)
    {
      $keySignature = 7 - $keySignature;
    }

    return $keySignature;
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
    for($voice = 0; $voice < 2; $voice++)
    {
      $nextNoteStart = intval($measure->getStart());
      $numberOfBeats = $this->readInt();

      for ($i = 0; $i < $numberOfBeats; $i++)
      {
        $nextNoteStart += $this->readBeat($nextNoteStart, $measure, $track, $tempo, $voice);
        if($i>256)
        {
          $message = sprintf('%s: Too much beats (%s) in measure %s of Track[%s], tempo %s'
            , __METHOD__, $numberOfBeats, $measure->getNumber(), $track->getName(), $tempo->getValue());
          throw new \Exception($message);
        }
      }
    }

    $emptyBeats = array();

    for($i = 0; $i < $measure->countBeats(); $i++)
    {
      $beat = $measure->getBeat($i);
      $empty = true;
      for($v = 0; $v < $beat->countVoices(); $v++)
      {
        if(!$beat->getVoice($v)->isEmpty())
        {
          $empty = false;
        }
      }
      if($empty)
      {
        $emptyBeats[] = $beat;
      }
    }

    foreach($emptyBeats as $beat)
    {
      $measure->remove($beat);
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
  private function readMeasureHeader($index, TimeSignature $timeSignature, $tempoValue = 120)
  {
    $flags = $this->readUnsignedByte();
    $header = new MeasureHeader();
    $header->setNumber($index + 1);
    $header->setStart(0);
    $header->getTempo()->setValue($tempoValue);
    $header->setRepeatOpen(($flags & 0x04) != 0);

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
      $header->setRepeatClose(($this->readByte() & 0xff) - 1);
    }

    if (($flags & 0x20) != 0)
    {
      $header->setMarker($this->readMarker($header->getNumber()));
    }

    if (($flags & 0x10) != 0)
    {
      $header->setRepeatAlternative($this->readUnsignedByte());
    }

    if (($flags & 0x40) != 0)
    {
      $this->keySignature = $this->readKeySignature();
      $this->skip(1);
    }

    if (($flags & 0x01) != 0 || ($flags & 0x02) != 0)
    {
      $this->skip(4);
    }
    if (($flags & 0x10) == 0)
    {
      $this->skip(1);
    }

    $tripletFeel = $this->readByte();

    if($tripletFeel == 1)
    {
      $header->setTripletFeel(MeasureHeader::TRIPLET_FEEL_EIGHTH);
    }
    else if($tripletFeel == 2)
    {
      $header->setTripletFeel(MeasureHeader::TRIPLET_FEEL_SIXTEENTH);
    }
    else
    {
      $header->setTripletFeel(MeasureHeader::TRIPLET_FEEL_NONE);
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

    for ($i = 0; $i < $count; $i++) 
    {
      if($i > 0)
      {
        $this->skip();
      }

      $song->addMeasureHeader($this->readMeasureHeader($i, $timeSignature));
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
        if($i != $measures - 1 || $j != $tracks - 1)
        {
          $this->skip();
        }
      }

      $header->getTempo()->copyFrom($tempo);
      $start += $header->getLength();
    }
  }

  /**
   * Reads mix change
   * 
   * @param Tempo $tempo
   * @return void
   */
  private function readMixChange(Tempo $tempo)
  {
    $this->readByte();
    
    $this->skip(16);
    $volume = $this->readByte();
    $pan = $this->readByte();
    $chorus = $this->readByte();
    $reverb = $this->readByte();
    $phaser = $this->readByte();
    $tremolo = $this->readByte();
    $this->readStringByteSizeOfInteger();
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
      if($this->getVersionIndex() > 0)
      {
        $this->skip();
      }
    }
    
    $this->skip(2);
    
    if($this->getVersionIndex() > 0)
    {
      $this->readStringByteSizeOfInteger();
      $this->readStringByteSizeOfInteger();
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
    $note->getEffect()->setAccentuatedNote((($flags & 0x40) != 0));
    $note->getEffect()->setHeavyAccentuatedNote((($flags & 0x02) != 0));
    $note->getEffect()->setGhostNote((($flags & 0x04) != 0));

    if (($flags & 0x20) != 0)
    {
      $noteType = $this->readUnsignedByte();
      $note->setTiedNote($noteType == 0x02);
      $note->getEffect()->setDeadNote($noteType == 0x03);
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

    if (($flags & 0x01) != 0)
    {
      $this->skip(8);
    }
    
    $this->skip();
    
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
  private function readNoteEffects(NoteEffect $noteEffect)
  {
    $flags1 = intval($this->readUnsignedByte());
    $flags2 = intval($this->readUnsignedByte());

    if (($flags1 & 0x01) != 0)
    {
      $this->readBend($noteEffect);
    }

    if (($flags1 & 0x10) != 0)
    {
      $this->readGrace($noteEffect);
    }

    if (($flags2 & 0x04) != 0)
    {
      $this->readTremoloPicking($noteEffect);
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
   * @return void
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
   * 
   * @param Song $song
   * @param integer $number
   * @param array $channels an array of Channel objects
   * @param Lyric $lyrics
   * @return Track
   */
  private function readTrack(Song $song, $number, $channels, $lyrics)
  {
    $this->readUnsignedByte();
    if($number == 1 || $this->getVersionIndex() == 0)
    {
      $this->skip();
    }

    $track = new Track();
    $track->setSong($song);
    $track->setNumber($number);
    $track->setLyrics($lyrics);
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

    $this->skip($this->getVersionIndex() > 0 ? 49 : 44);

    if($this->getVersionIndex() > 0)
    {
      $this->readStringByteSizeOfInteger();
      $this->readStringByteSizeOfInteger();
    }

    return $track;
  }

  /**
   * Loops on tracks to read
   * 
   * @param Song $song
   * @param int $count
   * @param array $channels array of channels
   * @param Lyric $lyric
   * @param integer $lyricTrack
   * @return void
   */
  private function readTracks(Song $song, $count, array $channels, Lyric $lyric, $lyricTrack)
  {
    for ($number = 1; $number <= $count; $number++)
    {
      $song->addTrack(
        $this->readTrack($song, $number, $channels
        , $number == $lyricTrack ? $lyric : new Lyric())
      );
    }

    $this->skip($this->getVersionIndex() == 0 ? 2 : 1);
  }

  /**
   * Reads tremolo bar
   * 
   * @param NoteEffect $noteEffect
   * @return void
   */
  private function readTremoloBar(NoteEffect $effect)
  {
    $tremoloBar = new EffectTremoloBar();
    $this->skip(5);
    $points = $this->readInt();

    for ($i = 0; $i < $points; $i++)
    {
      $position = $this->readInt();
      $value = $this->readInt();
      $this->readByte();

      $pointPosition = round($position * EffectTremoloBar::MAX_POSITION_LENGTH / GuitarProReaderInterface::GP_BEND_POSITION);
      $pointValue = round($value / (GuitarProReaderInterface::GP_BEND_SEMITONE * 2));
      $tremoloBar->addPoint($pointPosition, $pointValue);
    }

    if(count($tremoloBar->getPoints()))
    {
      $effect->setTremoloBar($tremoloBar);
    }
  }

  /**
   * Reads tremolo picking
   * 
   * @param NoteEffect $noteEffect
   * @return void
   */
  public function readTremoloPicking(NoteEffect $noteEffect)
  {
    $value = $this->readUnsignedByte();
    $tremoloPicking = new EffectTremoloPicking();
    if($value == 1)
    {
      $tremoloPicking->getDuration()->setValue(Duration::EIGHTH);
      $noteEffect->setTremoloPicking($tremoloPicking);
    }
    else if($value == 2)
    {
      $tremoloPicking->getDuration()->setValue(Duration::SIXTEENTH);
      $noteEffect->setTremoloPicking($tremoloPicking);
    }
    else if($value == 3)
    {
      $tremoloPicking->getDuration()->setValue(Duration::THIRTY_SECOND);
      $noteEffect->setTremoloPicking($tremoloPicking);
    }
  }

  /**
   * Reads trill effect
   * 
   * @param NoteEffect $effect
   * @return void
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
