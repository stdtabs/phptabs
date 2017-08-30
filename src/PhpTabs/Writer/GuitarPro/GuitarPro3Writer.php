<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro;

use Exception;
use PhpTabs\Music\Beat;
use PhpTabs\Music\Channel;
use PhpTabs\Music\Color;
use PhpTabs\Music\DivisionType;
use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\Marker;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Model\MeasureVoiceJoiner;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Note;
use PhpTabs\Music\Song;
use PhpTabs\Music\Stroke;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Text;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\Music\Velocities;
use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface as GprInterface;

class GuitarPro3Writer extends GuitarProWriterBase
{
  /** @constant version */
  const VERSION = 'FICHIER GUITAR PRO v3.00';

  /**
   * @param \PhpTabs\Music\Song $song
   */
  public function __construct(Song $song)
  {
    parent::__construct();

    if ($song->isEmpty())
    {
      throw new Exception('Song is empty');
    }

    $this->configureChannelRouter($song);
    $header = $song->getMeasureHeader(0);
    $this->writeStringByte(self::VERSION, 30);
    $this->writeInformations($song);
    $this->writeBoolean(
      $header->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_EIGHTH
    );
    $this->writeInt($header->getTempo()->getValue());
    $this->writeInt(0);
    $this->writeChannels($song);
    $this->writeInt($song->countMeasureHeaders());
    $this->writeInt($song->countTracks());
    $this->writeMeasureHeaders($song);
    $this->writeTracks($song);
    $this->writeMeasures($song, clone $header->getTempo());
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * 
   * @return array
   */
  private function makeChannels(Song $song)
  {
    $channels = array();
    for ($i = 0; $i < 64; $i++)
    {
      $channels[$i] = new Channel();
      $channels[$i]->setProgram(
        $i == Channel::DEFAULT_PERCUSSION_CHANNEL
            ? Channel::DEFAULT_PERCUSSION_PROGRAM : 24
      );
      $channels[$i]->setVolume(13);
      $channels[$i]->setBalance(8);
      $channels[$i]->setChorus(0);
      $channels[$i]->setReverb(0);
      $channels[$i]->setPhaser(0);
      $channels[$i]->setTremolo(0);
    }

    $songChannels = $song->getChannels();

    foreach ($songChannels as $channel)
    {
      $channelRoute = $this->getChannelRoute($channel->getChannelId());
      $channels[$channelRoute->getChannel1()]->setProgram($channel->getProgram());
      $channels[$channelRoute->getChannel1()]->setVolume($channel->getVolume());
      $channels[$channelRoute->getChannel1()]->setBalance($channel->getBalance());

      $channels[$channelRoute->getChannel2()]->setProgram($channel->getProgram());
      $channels[$channelRoute->getChannel2()]->setVolume($channel->getVolume());
      $channels[$channelRoute->getChannel2()]->setBalance($channel->getBalance());
    }

    return $channels;
  }

  /**
   * @param \PhpTabs\Music\Duration $duration
   * 
   * @return int
   */
  private function parseDuration(Duration $duration)
  {
    $value = 0;
    switch ($duration->getValue())
    {
      case Duration::WHOLE:
        $value = -2;
        break;
      case Duration::HALF:
        $value = -1;
        break;
      case Duration::QUARTER:
        $value = 0;
        break;
      case Duration::EIGHTH:
        $value = 1;
        break;
      case Duration::SIXTEENTH:
        $value = 2;
        break;
      case Duration::THIRTY_SECOND:
        $value = 3;
        break;
      case Duration::SIXTY_FOURTH:
        $value = 4;
        break;
    }
    return $value;
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
   * @param \PhpTabs\Music\Measure $measure
   * @param bool $changeTempo
   */
  private function writeBeat(Beat $beat, Measure $measure, $changeTempo)
  {
    $voice = $beat->getVoice(0);
    $duration = $voice->getDuration();
    $flags = 0;

    if ($duration->isDotted() || $duration->isDoubleDotted()) 
    {
      $flags |= 0x01;
    }

    if (!$duration->getDivision()->isEqual(DivisionType::normal()))
    {
      $flags |= 0x20;
    }

    if ($beat->isTextBeat())
    {
      $flags |= 0x04;
    }

    if ($changeTempo)
    {
      $flags |= 0x10;
    }

    $effect = null;
    if ($voice->isRestVoice())
    {
      $flags |= 0x40;
    }
    elseif ($voice->countNotes() > 0)
    {
      $note = $voice->getNote(0);
      $effect = $note->getEffect();

      if ($effect->isVibrato()
        || $effect->isTremoloBar()
        || $effect->isTapping()
        || $effect->isSlapping()
        || $effect->isPopping()
        || $effect->isHarmonic()
        || $effect->isFadeIn()
        || $beat->getStroke()->getDirection() != Stroke::STROKE_NONE)
      {
        $flags |= 0x08;
      }
    }

    $this->writeUnsignedByte($flags);

    if (($flags & 0x40) != 0)
    {
      $this->writeUnsignedByte(2);
    }

    $this->writeByte($this->parseDuration($duration));

    if (($flags & 0x20) != 0)
    {
      $this->writeInt($duration->getDivision()->getEnters());
    }

    if (($flags & 0x04) != 0)
    {
      $this->writeText($beat->getText());
    }

    if (($flags & 0x08) != 0)
    {
      $this->writeBeatEffects($beat, $effect);
    }

    if (($flags & 0x10) != 0)
    {
      $this->writeMixChange($measure->getTempo());
    }

    $stringFlags = 0;

    if (!$voice->isRestVoice())
    {
      for ($i = 0; $i < $voice->countNotes(); $i++)
      {
        $playedNote = $voice->getNote($i);
        $string = (7 - $playedNote->getString());
        $stringFlags |= (1 << $string);
      }
    }

    $this->writeUnsignedByte($stringFlags);

    for ($i = 6; $i >= 0; $i--)
    {
      if (($stringFlags & (1 << $i)) != 0)
      {
        for ($n = 0; $n < $voice->countNotes(); $n++)
        {
          $playedNote = $voice->getNote( $n );
          if ($playedNote->getString() == (6 - $i + 1))
          {
            $this->writeNote($playedNote);
            break;
          }
        }
      }
    }
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  private function writeBeatEffects(Beat $beat, NoteEffect $noteEffect)
  {
    $flags = 0;
    if ($noteEffect->isVibrato())
    {
      $flags += 0x01;
    }

    if ($noteEffect->isTremoloBar() || $noteEffect->isTapping() 
       || $noteEffect->isSlapping() || $noteEffect->isPopping())
    {
      $flags += 0x20;
    }

    if ($beat->getStroke()->getDirection() != Stroke::STROKE_NONE)
    {
      $flags |= 0x40;
    }

    if ($noteEffect->isHarmonic() && $noteEffect->getHarmonic()->getType() == EffectHarmonic::TYPE_NATURAL)
    {
      $flags += 0x04;
    }

    if ($noteEffect->isHarmonic() && $noteEffect->getHarmonic()->getType() != EffectHarmonic::TYPE_NATURAL)
    {
      $flags += 0x08;
    }

    if ($noteEffect->isFadeIn())
    { 
      $flags += 0x10;
    }

    $this->writeUnsignedByte($flags);

    if (($flags & 0x20) != 0)
    {
      if ($noteEffect->isTremoloBar())
      {
        $this->writeUnsignedByte(0);
        $this->writeInt(100);
      }
      elseif ($noteEffect->isTapping())
      {
        $this->writeUnsignedByte(1);
        $this->writeInt(0);
      }
      elseif ($noteEffect->isSlapping())
      {
        $this->writeUnsignedByte(2);
        $this->writeInt(0);
      }
      elseif ($noteEffect->isPopping())
      {
        $this->writeUnsignedByte(3);
        $this->writeInt(0);
      }
    }

    if (($flags & 0x40) != 0)
    {
      $this->writeUnsignedByte(
        $beat->getStroke()->getDirection() == Stroke::STROKE_DOWN 
          ? $this->toStrokeValue($beat->getStroke()) : 0
      );
      $this->writeUnsignedByte(
        $beat->getStroke()->getDirection() == Stroke::STROKE_UP 
          ? $this->toStrokeValue($beat->getStroke()) : 0
      );
    }
  }

  /**
   * @param \PhpTabs\Music\EffectBend $bend
   */
  private function writeBend(EffectBend $bend)
  {
    $points = count($bend->getPoints());
    $this->writeByte(1);
    $this->writeInt(0);
    $this->writeInt($points);
    for ($i = 0; $i < $points; $i++)
    {
      $point = $bend->getPoints()[$i];
      $this->writeInt(
        intval($point->getPosition() * GprInterface::GP_BEND_POSITION / EffectBend::MAX_POSITION_LENGTH)
      );
      $this->writeInt(
        intval($point->getValue() * GprInterface::GP_BEND_SEMITONE / EffectBend::SEMITONE_LENGTH)
      );
      $this->writeByte(0);
    }
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeChannels(Song $song)
  {
    $channels = $this->makeChannels($song);
    for ($i = 0; $i < count($channels); $i++)
    {
      $this->writeInt($channels[$i]->getProgram());
      $this->writeByte($this->toChannelByte($channels[$i]->getVolume()));
      $this->writeByte($this->toChannelByte($channels[$i]->getBalance()));
      $this->writeByte($this->toChannelByte($channels[$i]->getChorus()));
      $this->writeByte($this->toChannelByte($channels[$i]->getReverb()));
      $this->writeByte($this->toChannelByte($channels[$i]->getPhaser()));
      $this->writeByte($this->toChannelByte($channels[$i]->getTremolo()));
      $this->skipBytes(2);
    }
  }

  /**
   * @param \PhpTabs\Music\Color $color
   */
  private function writeColor(Color $color)
  {
    $this->writeUnsignedByte($color->getR());
    $this->writeUnsignedByte($color->getG());
    $this->writeUnsignedByte($color->getB());
    $this->writeByte(0);
  }

  /**
   * @param \PhpTabs\Music\EffectGrace $grace
   */
  private function writeGrace(EffectGrace $grace)
  {
    if ($grace->isDead())
    {
      $this->writeUnsignedByte(255);
    }
    else
    {
      $this->writeUnsignedByte($grace->getFret());
    }

    $this->writeUnsignedByte(
      intval((($grace->getDynamic() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1)
    );

    if ($grace->getTransition() == EffectGrace::TRANSITION_NONE)
    {
      $this->writeUnsignedByte(0);
    }
    elseif ($grace->getTransition() == EffectGrace::TRANSITION_SLIDE)
    {
      $this->writeUnsignedByte(1);
    }
    elseif ($grace->getTransition() == EffectGrace::TRANSITION_BEND)
    {
      $this->writeUnsignedByte(2);
    }
    elseif ($grace->getTransition() == EffectGrace::TRANSITION_HAMMER)
    {
      $this->writeUnsignedByte(3);
    }

    $this->writeUnsignedByte($grace->getDuration());
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeInformations(Song $song)
  {
    $this->writeStringByteSizeOfInteger($song->getName());
    $this->writeStringByteSizeOfInteger("");
    $this->writeStringByteSizeOfInteger($song->getArtist());
    $this->writeStringByteSizeOfInteger($song->getAlbum());
    $this->writeStringByteSizeOfInteger($song->getAuthor());
    $this->writeStringByteSizeOfInteger($song->getCopyright());
    $this->writeStringByteSizeOfInteger($song->getWriter());
    $this->writeStringByteSizeOfInteger("");

    $comments = $this->toCommentLines($song->getComments());
    $this->writeInt(count($comments));
    for ($i = 0; $i < count($comments); $i++)
    {
      $this->writeStringByteSizeOfInteger($comments[$i]);
    }
  }

  /**
   * @param \PhpTabs\Music\Marker $marker
   */
  private function writeMarker(Marker $marker)
  {
    $this->writeStringByteSizeOfInteger($marker->getTitle());
    $this->writeColor($marker->getColor());
  }

  /**
   * @param \PhpTabs\Music\Measure $measure
   * @param bool $changeTempo
   */
  private function writeMeasure(Measure $srcMeasure, $changeTempo)
  {
    $measure = (new MeasureVoiceJoiner($srcMeasure))->process();

    $beatCount = $measure->countBeats();
    $this->writeInt($beatCount);

    for ($i = 0; $i < $beatCount; $i++)
    {
      $beat = $measure->getBeat($i);
      $this->writeBeat($beat, $measure, ($changeTempo && $i == 0) );
    }
  }

  /**
   * @param \PhpTabs\Music\MeasureHeader $measure
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   */
  private function writeMeasureHeader(MeasureHeader $measure, TimeSignature $timeSignature)
  {
    $flags = 0;

    if ($measure->getNumber() == 1 || $measure->getTimeSignature()->getNumerator() != $timeSignature->getNumerator())
    {
      $flags |= 0x01;
    }

    if ($measure->getNumber() == 1 || $measure->getTimeSignature()->getDenominator()->getValue() != $timeSignature->getDenominator()->getValue())
    {
      $flags |= 0x02;
    }

    if ($measure->isRepeatOpen())
    {
      $flags |= 0x04;
    }

    if ($measure->getRepeatClose() > 0)
    {
      $flags |= 0x08;
    }

    if ($measure->hasMarker())
    {
      $flags |= 0x20;
    }

    $this->writeUnsignedByte($flags);

    if (($flags & 0x01) != 0)
    {
      $this->writeByte($measure->getTimeSignature()->getNumerator());
    }

    if (($flags & 0x02) != 0)
    {
      $this->writeByte($measure->getTimeSignature()->getDenominator()->getValue());
    }

    if (($flags & 0x08) != 0)
    {
      $this->writeByte($measure->getRepeatClose());
    }

    if (($flags & 0x20) != 0)
    {
      $this->writeMarker($measure->getMarker());
    }
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeMeasureHeaders(Song $song)
  {
    $timeSignature = new TimeSignature();
    if ($song->countMeasureHeaders() > 0)
    {
      for ($i = 0; $i < $song->countMeasureHeaders(); $i++)
      {
        $header = $song->getMeasureHeader($i);
        $this->writeMeasureHeader($header, $timeSignature);
        $timeSignature->setNumerator($header->getTimeSignature()->getNumerator());
        $timeSignature->getDenominator()->setValue(
          $header->getTimeSignature()->getDenominator()->getValue()
        );
      }
    }
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\Tempo $tempo
   */
  private function writeMeasures(Song $song, Tempo $tempo)
  {
    for ($i = 0; $i < $song->countMeasureHeaders(); $i++)
    {
      $header = $song->getMeasureHeader($i);

      for ($j = 0; $j < $song->countTracks(); $j++)
      {
        $track = $song->getTrack($j);
        $measure = $track->getMeasure($i);
        $this->writeMeasure($measure, $header->getTempo()->getValue() != $tempo->getValue());
      }

      $tempo->copyFrom($header->getTempo());
    }
  }

  /**
   * @param \PhpTabs\Music\Note $note
   */
  private function writeNote(Note $note)
  {
    $flags = 0x20 | 0x10;

    if ($note->getEffect()->isGhostNote())
    {
      $flags |= 0x04;
    }

    if ($note->getEffect()->isBend()
        || $note->getEffect()->isGrace() 
        || $note->getEffect()->isSlide()
        || $note->getEffect()->isHammer()
        || $note->getEffect()->isLetRing())
    {
      $flags |= 0x08;
    }
    $this->writeUnsignedByte($flags);

    if (($flags & 0x20) != 0)
    {
      $typeHeader = 0x01;
      if ($note->isTiedNote())
      {
        $typeHeader = 0x02;
      }
      elseif ($note->getEffect()->isDeadNote())
      {
        $typeHeader = 0x03;
      }
      $this->writeUnsignedByte($typeHeader);
    }

    if (($flags & 0x10) != 0)
    {
      $this->writeByte(intval((($note->getVelocity() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1));
    }

    if (($flags & 0x20) != 0)
    {
      $this->writeByte($note->getValue());
    }

    if (($flags & 0x08) != 0)
    {
      $this->writeNoteEffects($note->getEffect());
    }
  }

  /**
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  private function writeNoteEffects(NoteEffect $effect)
  {
    $flags = 0;
    if ($effect->isBend())
    {
      $flags |= 0x01;
    }

    if ($effect->isHammer())
    {
      $flags |= 0x02;
    }

    if ($effect->isSlide())
    {
      $flags |= 0x04;
    }

    if ($effect->isLetRing())
    {
      $flags |= 0x08;
    }

    if ($effect->isGrace())
    {
      $flags |= 0x10;
    }

    $this->writeUnsignedByte($flags);

    if (($flags & 0x01) != 0)
    {
      $this->writeBend($effect->getBend());
    }

    if (($flags & 0x10) != 0)
    {
      $this->writeGrace($effect->getGrace());
    }
  }

  /**
   * @param int $short
   * 
   * @return int
   */
  private function toChannelByte($short)
  {
    return intval(($short + 1) / 8);
  }

  /**
   * @param string $comments
   * 
   * @return array
   */
  private function toCommentLines($comments)
  {
    $lines = array();

    $line = $comments;

    while (strlen($line) > 127)
    {
      $subline = substr($line, 0, 127);
      $lines[] = $subline;
      $line = substr($line, 127);
    }

    $lines[] = $line;

    return $lines;
  }

  /**
   * @param \PhpTabs\Music\Stroke $stroke
   * 
   * @return int
   */
  private function toStrokeValue(Stroke $stroke)
  {
    if ($stroke->getValue() == Duration::SIXTY_FOURTH)
    {
      return 2;
    }

    if ($stroke->getValue() == Duration::THIRTY_SECOND)
    {
      return 3;
    }

    if ($stroke->getValue() == Duration::SIXTEENTH)
    {
      return 4;
    }

    if ($stroke->getValue() == Duration::EIGHTH)
    {
      return 5;
    }

    if ($stroke->getValue() == Duration::QUARTER)
    {
      return 6;
    }

    return 2;
  }

  /**
   * @param \PhpTabs\Music\Text $text
   */
  private function writeText(Text $text)
  {
    $this->writeStringByteSizeOfInteger($text->getValue());
  }

  /**
   * @param \PhpTabs\Music\Track $track
   */
  private function writeTrack(Track $track)
  {
    $channel = $this->getChannelRoute($track->getChannelId());

    $flags = 0;
    if ($track
          ->getSong()
          ->getChannelById($track->getChannelId())
          ->isPercussionChannel()
    ) {
      $flags |= 0x01;
    }

    $this->writeUnsignedByte($flags);

    $this->writeStringByte($track->getName(), 40);
    $this->writeInt(count($track->getStrings()));
    for ($i = 0; $i < 7; $i++)
    {
      $value = 0;
      if (count($track->getStrings()) > $i)
      {
        $string = $track->getStrings()[$i];
        $value = $string->getValue();
      }
      $this->writeInt($value);
    }
    $this->writeInt(1);
    $this->writeInt($channel->getChannel1() + 1);
    $this->writeInt($channel->getChannel2() + 1);
    $this->writeInt(24);
    $this->writeInt(min(max($track->getOffset(),0),12));
    $this->writeColor($track->getColor());
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeTracks(Song $song)
  {
    for ($i = 0; $i < $song->countTracks(); $i++)
    {
      $track = $song->getTrack($i);
      $this->writeTrack($track);
    }
  }
}
