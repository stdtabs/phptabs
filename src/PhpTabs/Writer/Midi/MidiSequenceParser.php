<?php

namespace PhpTabs\Writer\Midi;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Channel;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectBend;
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Note;
use PhpTabs\Model\Song;
use PhpTabs\Model\Stroke;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\Track;
use PhpTabs\Model\Voice;

class MidiSequenceParser
{
  const DEFAULT_METRONOME_KEY = 37;
  const DEFAULT_DURATION_PM = 60;
  const DEFAULT_DURATION_DEAD = 30;
  const DEFAULT_BEND = 64;
  const DEFAULT_BEND_SEMI_TONE = 2.75;

  private $song;
  private $flags;
  private $infoTrack;
  private $metronomeTrack;
  private $metronomeChannelId;
  private $firstTickMove;
  private $tempoPercent;
  private $transpose;
  private $sHeader;
  private $eHeader;

	public function __construct(Song $song, $flags)
  {
		$this->song = $song;
		$this->flags = $flags;
		$this->tempoPercent = 100;
		$this->transpose = 0;
		$this->sHeader = -1;
		$this->eHeader = -1;
		$this->firstTickMove = ($flags & 0x08) != 0
      ? -Duration::QUARTER_TIME : 0;
	}

  public function getInfoTrack()
  {
    return $this->infoTrack;
  }

  public function getMetronomeTrack()
  {
    return $this->metronomeTrack;
  }

  private function getTick($tick)
  {
    return $tick + $this->firstTickMove;
  }

  public function setSHeader($header)
  {
    $this->sHeader = $header;
  }

  public function setEHeader($header)
  {
    $this->eHeader = $header;
  }

  public function setMetronomeChannelId($metronomeChannelId)
  {
    $this->metronomeChannelId = $metronomeChannelId;
  }

  public function setTempoPercent($tempoPercent)
  {
    $this->tempoPercent = $tempoPercent;
  }

  public function setTranspose($transpose)
  {
    $this->transpose = $transpose;
  }

  private function fix( $value )
  {
    return $value >= 0 ? 
      ($value <= 127 ? $value : 127) : 0;
  }

  public function parse(MidiSequenceHandler $sequence)
  {
    $this->infoTrack = 0;
    $this->metronomeTrack = $sequence->getTracks() - 1;

    $helper = new MidiSequenceHelper($sequence);
    $controller = new MidiRepeatController($this->song, $this->sHeader, $this->eHeader);

    while(!$controller->finished())
    {
      $index = $controller->getIndex();
      $move = $controller->getRepeatMove();
      $controller->process();

      if($controller->shouldPlay())
      {
        $helper->addMeasureHelper( new MidiMeasureHelper($index, $move) );
      }
    }

    $this->addDefaultMessages($helper, $this->song);

    for ($i = 0; $i < $this->song->countTracks(); $i++)
    {
      $songTrack = $this->song->getTrack($i);
      $this->createTrack($helper, $songTrack);
    }

    $sequence->notifyFinish();
  }

  private function createTrack(MidiSequenceHelper $helper, Track $track)
  {
    $channel = $this->song->getChannelById($track->getChannelId());

    if( $channel !== null )
    {
      $previous = null;

      $this->addBend($helper, $track->getNumber(), Duration::QUARTER_TIME, self::DEFAULT_BEND, $channel->getChannelId(), -1, false);
      $this->makeChannel($helper, $channel, $track->getNumber());

      $mCount = count($helper->getMeasureHelpers());

      for( $mIndex = 0 ; $mIndex < $mCount ; $mIndex++ )
      {
        $measureHelper = $helper->getMeasureHelper( $mIndex );

        $measure = $track->getMeasure($measureHelper->getIndex());

        if($track->getNumber() == 1)
        {
          $this->addTimeSignature($helper, $measure, $previous, $measureHelper->getMove());
          $this->addTempo($helper, $measure, $previous, $measureHelper->getMove());
          $this->addMetronome($helper, $measure->getHeader(), $measureHelper->getMove() );
        }

        $this->makeBeats( $helper, $channel, $track, $measure, $mIndex, $measureHelper->getMove() );

        $previous = $measure;
      }
    }
  }

	private function makeBeats(MidiSequenceHelper $helper, Channel $channel, Track $track, Measure $measure, $mIndex, $startMove)
  {
		$stroke = array();
    for($i=0; $i<$track->countStrings(); $i++)
    {
      $stroke[] = 0;
    }
    
    $track->getStrings(); // array($track->countStrings());
		$previous = null;

		for ($bIndex = 0; $bIndex < $measure->countBeats(); $bIndex++)
    {
			$beat = $measure->getBeat($bIndex);
			$this->makeNotes( $helper, $channel, $track, $beat, $measure->getTempo(), $mIndex, $bIndex, $startMove, $this->getStroke($beat, $previous, $stroke) );
			$previous = $beat;
		}
	}

  private function makeNotes( MidiSequenceHelper $sHelper, Channel $channel, Track $track, Beat $beat, Tempo $tempo, $mIndex, $bIndex, $startMove, $stroke)
  {
    for( $vIndex = 0; $vIndex < $beat->countVoices(); $vIndex++ )
    {
      $voice = $beat->getVoice($vIndex);

      $tickHelper = $this->checkTripletFeel($voice, $bIndex);
      for ($noteIdx = 0; $noteIdx < $voice->countNotes(); $noteIdx++)
      {
        $note = $voice->getNote($noteIdx);
        if (!$note->isTiedNote())
        {
          $key = ($this->transpose + $track->getOffset() + $note->getValue() + $track->getStrings()[$note->getString() - 1]->getValue());

          $start = $this->applyStrokeStart($note, ($tickHelper->getStart() + $startMove), $stroke);
          $duration = $this->applyStrokeDuration($note, $this->getRealNoteDuration($sHelper, $track, $note, $tempo, $tickHelper->getDuration(), $mIndex, $bIndex), $stroke);

          $velocity = $this->getRealVelocity($sHelper, $note, $track, $channel, $mIndex, $bIndex);
          $channelId = $channel->getChannelId();
          $midiVoice = $note->getString();
          $bendMode = false;

          $percussionChannel = $channel->isPercussionChannel();
          //---Fade In---
          if($note->getEffect()->isFadeIn())
          {
            $this->makeFadeIn( $sHelper, $track->getNumber(), $start, $duration, $channel->getVolume(), $channelId);
          }
          //---Grace---
          if($note->getEffect()->isGrace() && !$percussionChannel )
          {
            $bendMode = true;
            $graceKey = $track->getOffset() + $note->getEffect()->getGrace()->getFret() + $track->getStrings()[$note->getString() - 1]->getValue();
            $graceLength = $note->getEffect()->getGrace()->getDurationTime();
            $graceVelocity = $note->getEffect()->getGrace()->getDynamic();
            $graceDuration = $note->getEffect()->getGrace()->isDead()
              ? $this->applyStaticDuration($tempo, self::DEFAULT_DURATION_DEAD, $graceLength) : $graceLength;

            if($note->getEffect()->getGrace()->isOnBeat() || ($start - $graceLength) < Duration::QUARTER_TIME)
            {
              $start += $graceLength;
              $duration -= $graceLength;
            }
            $this->makeNote($sHelper, $track->getNumber(), $graceKey->start - $graceLength, $graceDuration, $graceVelocity, $channelId, $midiVoice, $bendMode);

          }
          //---Trill---
          if($note->getEffect()->isTrill() && !$percussionChannel )
          {
            $trillKey = $track->getOffset() + $note->getEffect()->getTrill()->getFret() + $track->getStrings()[$note->getString() - 1]->getValue();
            $trillLength = $note->getEffect()->getTrill()->getDuration()->getTime();

            $realKey = true;
            $tick = $start;
            while(true)
            {
              if($tick + 10 >= ($start + $duration))
              {
                break;
              }
              else if( ($tick + $trillLength) >= ($start + $duration))
              {
                $trillLength = ((($start + $duration) - $tick) - 1);
              }
              $this->makeNote($sHelper, $track->getNumber(),($realKey ? $key : $trillKey), $tick, $trillLength, $velocity, $channelId, $midiVoice, $bendMode);
              $realKey = !$realKey;
              $tick += $trillLength;
            }

            continue;
          }
          //---Tremolo Picking---
          if($note->getEffect()->isTremoloPicking())
          {
            $tpLength = $note->getEffect()->getTremoloPicking()->getDuration()->getTime();
            $tick = $start;
            while(true)
            {
              if($tick + 10 >= ($start + $duration))
              {
                break ;
              }
              else if( ($tick + $tpLength) >= ($start + $duration))
              {
                $tpLength = ((($start + $duration) - $tick) - 1);
              }
              $this->makeNote($sHelper, $track->getNumber(), $key, $tick, $tpLength, $velocity, $channelId, $midiVoice, $bendMode);
              $tick += $tpLength;
            }
            continue;
          }

          //---Bend---
          if( $note->getEffect()->isBend() && !$percussionChannel )
          {
            $bendMode = true;
            $this->makeBend($sHelper, $track->getNumber(), $start, $duration, $note->getEffect()->getBend(), $channelId, $midiVoice, $bendMode);
          }
          //---TremoloBar---
          else if( $note->getEffect()->isTremoloBar() && !$percussionChannel )
          {
            $bendMode = true;
            $this->makeTremoloBar($sHelper, $track->getNumber(), $start, $duration, $note->getEffect()->getTremoloBar(), $channelId, $midiVoice, $bendMode);
          }
          //---Slide---
          else if( $note->getEffect()->isSlide() && !$percussionChannel)
          {
            $bendMode = true;
            $this->makeSlide($sHelper, $note, $track, $mIndex, $bIndex, $startMove, $channelId, $midiVoice, $bendMode);
          }
          //---Vibrato---
          else if( $note->getEffect()->isVibrato() && !$percussionChannel)
          {
            $bendMode = true;
            $this->makeVibrato($sHelper, $track->getNumber(), $start, $duration, $channelId, $midiVoice, $bendMode);
          }
          //---Harmonic---
          if( $note->getEffect()->isHarmonic() && !$percussionChannel)
          {
            $orig = $key;

            //Natural
            if($note->getEffect()->getHarmonic()->isNatural())
            {
              for($i = 0; $i < count(EffectHarmonic::$naturalFrequencies); $i++)
              {
                if(($note->getValue() % 12) ==  (EffectHarmonic::$naturalFrequencies[$i][0] % 12) )
                {
                  $key = $orig + EffectHarmonic::$naturalFrequencies[$i][1] - $note->getValue();
                  break;
                }
              }
            }
            //Artifical/Tapped/Pinch/Semi
            else
            {
              if( $note->getEffect()->getHarmonic()->isSemi() && !$percussionChannel )
              {
                $this->makeNote($sHelper, $track->getNumber(), min(127, orig), $start, $duration, max(Velocities::MIN_VELOCITY, $velocity - (Velocities::VELOCITY_INCREMENT * 3)), $channelId, $midiVoice, $bendMode);
              }
              $key = ($orig + EffectHarmonic::$naturalFrequencies[$note->getEffect()->getHarmonic()->getData()][1]);

            }
            if( ($key - 12) > 0 )
            {
              $hVelocity = max(Velocities::MIN_VELOCITY, $velocity - (Velocities::VELOCITY_INCREMENT * 4));
              $this->makeNote($sHelper, $track->getNumber(),($key - 12), $start, $duration, $hVelocity, $channelId, $midiVoice, $bendMode);
            }
          }

          //---Normal Note---
          $this->makeNote($sHelper, $track->getNumber(), min(127, $key), $start, $duration, $velocity, $channelId, $midiVoice, $bendMode);
        }
      }
    }
  }

  private function makeNote(MidiSequenceHelper $sHelper, $track, $key, $start, $duration, $velocity, $channel, $midiVoice, $bendMode)
  {
    $sHelper->getSequence()->addNoteOn($this->getTick($start), $track, $channel, $this->fix($key), $this->fix($velocity), $midiVoice, $bendMode);

    if( $duration > 0 )
    {
      $sHelper->getSequence()->addNoteOff($this->getTick($start + $duration), $track, $channel, $this->fix($key), $this->fix($velocity), $midiVoice, $bendMode);
    }
  }

  private function makeChannel(MidiSequenceHelper $sHelper, Channel $channel, $track)
  {
    if(($this->flags & MidiWriter::ADD_MIXER_MESSAGES) != 0)
    {
      $channelId = $channel->getChannelId();
      $tick = $this->getTick(Duration::QUARTER_TIME);
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::VOLUME,$this->fix($channel->getVolume()));
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::BALANCE,$this->fix($channel->getBalance()));
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::CHORUS,$this->fix($channel->getChorus()));
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::REVERB,$this->fix($channel->getReverb()));
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::PHASER,$this->fix($channel->getPhaser()));
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::TREMOLO,$this->fix($channel->getTremolo()));
      $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::EXPRESSION, 127);

      if(!$channel->isPercussionChannel())
      {
        $sHelper->getSequence()->addControlChange($tick, $track, $channelId, MidiWriter::BANK_SELECT, $this->fix($channel->getBank()));
      }

      $sHelper->getSequence()->addProgramChange($tick, $track, $channelId, $this->fix($channel->getProgram()));
    }
  }

  private function addTimeSignature(MidiSequenceHelper $sHelper, Measure $currMeasure, Measure $prevMeasure = null, $startMove)
  {
    $addTimeSignature = false;

    if ($prevMeasure === null)
    {
      $addTimeSignature = true;
    }
    else
    {
      $currNumerator = $currMeasure->getTimeSignature()->getNumerator();
      $currValue = $currMeasure->getTimeSignature()->getDenominator()->getValue();
      $prevNumerator = $prevMeasure->getTimeSignature()->getNumerator();
      $prevValue = $prevMeasure->getTimeSignature()->getDenominator()->getValue();
      if ($currNumerator != $prevNumerator || $currValue != $prevValue)
      {
        $addTimeSignature = true;
      }
    }
    if ($addTimeSignature)
    {
      $sHelper->getSequence()->addTimeSignature($this->getTick($currMeasure->getStart() + $startMove), $this->getInfoTrack(), $currMeasure->getTimeSignature());
    }
  }

  private function addTempo(MidiSequenceHelper $sHelper, Measure $currMeasure, Measure $prevMeasure = null, $startMove)
  {
    $addTempo = false;
    if ($prevMeasure === null)
    {
      $addTempo = true;
    }
    else
    {
      if ($currMeasure->getTempo()->getInTPQ() != $prevMeasure->getTempo()->getInTPQ())
      {
        $addTempo = true;
      }
    }
    if ($addTempo)
    {
      $usq = ($currMeasure->getTempo()->getInTPQ() * 100 / $this->tempoPercent );
      $sHelper->getSequence()->addTempoInUSQ($this->getTick($currMeasure->getStart() + $startMove), $this->getInfoTrack(), $usq);
    }
  }

  private function getRealNoteDuration(MidiSequenceHelper $sHelper, Track $track, Note $note, Tempo $tempo, $duration, $mIndex, $bIndex)
  {
    $letRing = ($note->getEffect()->isLetRing());
    $letRingBeatChanged = false;
    $lastEnd = ($note->getVoice()->getBeat()->getStart() + $note->getVoice()->getDuration()->getTime() + $sHelper->getMeasureHelper($mIndex)->getMove());
    $realDuration = $duration;
    $nextBIndex = ($bIndex + 1);
    $mCount = count($sHelper->getMeasureHelpers());
    for ($m = $mIndex; $m < $mCount; $m++)
    {
      $mh = $sHelper->getMeasureHelper( $m );
      $measure = $track->getMeasure( $mh->getIndex() );

      $beatCount = $measure->countBeats();
      for ($b = $nextBIndex; $b < $beatCount; $b++)
      {
        $beat = $measure->getBeat($b);
        $voice = $beat->getVoice($note->getVoice()->getIndex());
        if(!$voice->isEmpty())
        {
          if($voice->isRestVoice())
          {
            return $this->applyDurationEffects($note, $tempo, $realDuration);
          }
          $noteCount = $voice->countNotes();
          for ($n = 0; $n < $noteCount; $n++) 
          {
            $nextNote = $voice->getNote( $n );
            if (!$nextNote == $note || $mIndex != $m )
            {
              if ($nextNote->getString() == $note->getString())
              {
                if ($nextNote->isTiedNote())
                {
                  $realDuration += ($mh->getMove() + $beat->getStart() - $lastEnd) + ($nextNote->getVoice()->getDuration()->getTime());
                  $lastEnd = ($mh->getMove() + $beat->getStart() + $voice->getDuration()->getTime());
                  $letRing = ($nextNote->getEffect()->isLetRing());
                  $letRingBeatChanged = true;
                }
                else
                {
                  return $this->applyDurationEffects($note, $tempo, $realDuration);
                }
              }
            }
          }
            
          if($letRing && !$letRingBeatChanged)
          {
            $realDuration += ( $voice->getDuration()->getTime() );
          }
          $letRingBeatChanged = false;
        }
      }
      $nextBIndex = 0;
    }
    return $this->applyDurationEffects($note, $tempo, $realDuration);
  }

  private function applyDurationEffects(Note $note, Tempo $tempo, $duration)
  {
    //dead note
    if($note->getEffect()->isDeadNote())
    {
      return $this->applyStaticDuration($tempo, self::DEFAULT_DURATION_DEAD, $duration);
    }
    //palm mute
    if($note->getEffect()->isPalmMute())
    {
      return $this->applyStaticDuration(tempo, self::DEFAULT_DURATION_PM, $duration);
    }
    //staccato
    if($note->getEffect()->isStaccato())
    {
      return $duration * 50 / 100;
    }
    return $duration;
  }

  private function applyStaticDuration(Tempo $tempo, $duration, $maximum )
  {
    $value = $tempo->getValue() * $duration / 60;

    return $value < $maximum ? $value : $maximum;
  }

  private function getRealVelocity(MidiSequenceHelper $sHelper, Note $note, Track $track, Channel $channel, $mIndex, $bIndex)
  {
    $velocity = $note->getVelocity();

    //Check for Hammer effect
    if(!$channel->isPercussionChannel())
    {
      $previousNote = $this->getPreviousNote($sHelper, $note, $track, $mIndex, $bIndex, false);
      if($previousNote !== null && $previousNote->getNote()->getEffect()->isHammer())
      {
        $velocity = max(Velocities::MIN_VELOCITY, $velocity - 25);
      }
    }

    //Check for GhostNote effect
    if($note->getEffect()->isGhostNote())
    {
      $velocity = max(Velocities::MIN_VELOCITY, ($velocity - Velocities::VELOCITY_INCREMENT));
    }
    else if($note->getEffect()->isAccentuatedNote())
    {
      $velocity = max(Velocities::MIN_VELOCITY, ($velocity + Velocities::VELOCITY_INCREMENT));
    }
    else if($note->getEffect()->isHeavyAccentuatedNote())
    {
      $velocity = max(Velocities::MIN_VELOCITY, ($velocity + (Velocities::VELOCITY_INCREMENT * 2)));
    }

    return $velocity > 127 ? 127 : $velocity;
  }

  public function addMetronome(MidiSequenceHelper $sHelper, MeasureHeader $header, $startMove)
  {
    if( ($this->flags & MidiWriter::ADD_METRONOME) != 0)
    {
      if( $this->metronomeChannelId >= 0 )
      {
        $start = $startMove + $header->getStart();
        $length = $header->getTimeSignature()->getDenominator()->getTime();
        for($i = 1; $i <= $header->getTimeSignature()->getNumerator(); $i++)
        {
          $this->makeNote($sHelper, $this->getMetronomeTrack(), self::DEFAULT_METRONOME_KEY, $start, $length, Velocities::_DEFAULT, $this->metronomeChannelId, -1, false);
          $start += $length;
        }
      }
    }
  }

  public function addDefaultMessages(MidiSequenceHelper $sHelper, Song $song)
  {
    if( ($this->flags & MidiWriter::ADD_DEFAULT_CONTROLS) != 0)
    {
      $channels = $song->getChannels();
      foreach($channels as $channel)
      {
        $channelId = $channel->getChannelId();
        $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::RPN_MSB, 0);
        $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::RPN_LSB, 0);
        $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::DATA_ENTRY_MSB, 12);
        $sHelper->getSequence()->addControlChange($this->getTick(Duration::QUARTER_TIME), $this->getInfoTrack(), $channelId, MidiWriter::DATA_ENTRY_LSB, 0);
      }
    }
  }

  private function addBend(MidiSequenceHelper $sHelper, $track, $tick, $bend, $channel, $midiVoice, $bendMode)
  {
    $sHelper->getSequence()->addPitchBend($this->getTick($tick), $track, $channel, $this->fix($bend), $midiVoice, $bendMode);
  }

  public function makeVibrato(MidiSequenceHelper $sHelper, $track, $start, $duration, $channel, $midiVoice, $bendMode)
  {
    $nextStart = $start;
    $end = $nextStart + $duration;

    while($nextStart < $end)
    {
      $nextStart = ($nextStart + 160 > $end)
        ? $end : ($nextStart + 160);

      $this->addBend($sHelper, $track, $nextStart, self::DEFAULT_BEND, $channel, $midiVoice, $bendMode);

      $nextStart = ($nextStart + 160 > $end)
        ? $end : ($nextStart + 160);

      $this->addBend($sHelper, $track, $nextStart, self::DEFAULT_BEND + intval(self::DEFAULT_BEND_SEMI_TONE / 2), $channel, $midiVoice, $bendMode);
    }

    $this->addBend($sHelper, $track, $nextStart, self::DEFAULT_BEND, $channel, $midiVoice, $bendMode);
  }

  public function makeBend(MidiSequenceHelper $sHelper, $track, $start, $duration, EffectBend $bend, $channel, $midiVoice, $bendMode)
  {
    $points = $bend->getPoints();
    for($i=0; $i<count($points); $i++)
    {
      $point = $points[$i];
      $bendStart = $start + $point->getTime($duration);
      $value = self::DEFAULT_BEND + intval($point->getValue() * self::DEFAULT_BEND_SEMI_TONE / EffectBend::SEMITONE_LENGTH);
      $value = $value <= 127 ? $value : 127;
      $value = $value >= 0 ? $value : 0;
      $this->addBend($sHelper, $track, $bendStart, $value, $channel, $midiVoice, $bendMode);

      if(count($points) > $i + 1)
      {
        $nextPoint = $points[$i + 1];
        $nextValue = self::DEFAULT_BEND + intval($nextPoint->getValue() * self::DEFAULT_BEND_SEMI_TONE / EffectBend::SEMITONE_LENGTH);
        $nextBendStart = $start + $nextPoint->getTime($duration);
        if($nextValue != $value)
        {
          $width = ($nextBendStart - $bendStart) / abs($nextValue - $value);
          //asc
          if($value < $nextValue)
          {
            while($value < $nextValue)
            {
              $value++;
              $bendStart += $width;
              $this->addBend($sHelper, $track, $bendStart,($value <= 127 ? $value : 127), $channel, $midiVoice, $bendMode);
            }
            //desc
          }
          else if($value > $nextValue)
          {
            while($value > $nextValue)
            {
              $value--;
              $bendStart += $width;
              $this->addBend($sHelper, $track, $bendStart,($value >= 0 ? $value : 0), $channel, $midiVoice, $bendMode);
            }
          }
        }
      }
    }
    $this->addBend($sHelper, $track, $start + $duration, self::DEFAULT_BEND, $channel, $midiVoice, $bendMode);
  }

  public function makeTremoloBar(MidiSequenceHelper $sHelper, $track, $start, $duration, EffectTremoloBar $effect, $channel, $midiVoice, $bendMode)
  {
    $points = $effect->getPoints();
    for($i=0; $i<count($points); $i++)
    {
      $point = $points[$i];
      $pointStart = $start + $point->getTime($duration);
      $value = self::DEFAULT_BEND + intval($point->getValue() * self::DEFAULT_BEND_SEMI_TONE * 2);
      $value = $value <= 127 ? $value : 127;
      $value = $value >= 0 ? $value : 0;
      $this->addBend($sHelper, $track, $pointStart, $value, $channel, $midiVoice, $bendMode);
      if(count($points) > $i + 1)
      {
        $nextPoint = $points[$i + 1];
        $nextValue = self::DEFAULT_BEND + intval($nextPoint->getValue() * self::DEFAULT_BEND_SEMI_TONE * 2);
        $nextPointStart = $start + $nextPoint->getTime($duration);
        if($nextValue != $value)
        {
          $width = ($nextPointStart - $pointStart) / abs($nextValue - $value);
          //asc
          if($value < $nextValue)
          {
            while($value < $nextValue)
            {
              $value++;
              $pointStart += $width;
              $this->addBend($sHelper, $track, $pointStart,($value <= 127 ? $value : 127), $channel, $midiVoice, $bendMode);
            }
          //desc
          }
          else if($value > $nextValue)
          {
            while($value > $nextValue)
            {
              $value--;
              $pointStart += $width;
              $this->addBend($sHelper, $track, $pointStart,($value >= 0 ? $value : 0), $channel, $midiVoice, $bendMode);
            }
          }
        }
      }
    }
    $this->addBend($sHelper, $track, $start + $duration, self::DEFAULT_BEND, $channel, $midiVoice, $bendMode);
  }

  private function makeSlide(MidiSequenceHelper $sHelper, Note $note, Track $track, $mIndex, $bIndex, $startMove, $channel, $midiVoice, $bendMode)
  {
    $nextNote = $this->getNextNote($sHelper, $note, $track, $mIndex, $bIndex, true);
    if( $nextNote !== null )
    {
      $value1 = $note->getValue();
      $value2 = $nextNote->getNote()->getValue();

      $tick1 = $note->getVoice()->getBeat()->getStart() + $startMove;
      $tick2 = $nextNote->getNote()->getVoice()->getBeat()->getStart() + $nextNote->getMeasure()->getMove();

      // Make the Slide
      $this->makeMidiSlide($sHelper, $track->getNumber(), $tick1, $value1, $tick2, $value2, $channel, $midiVoice, $bendMode);
      // Normalize the Bend
      $this->addBend($sHelper, $track->getNumber(), $tick2 , self::DEFAULT_BEND, $channel, $midiVoice, $bendMode);
    }
  }
	
  public function makeMidiSlide(MidiSequenceHelper $sHelper, $track, $tick1, $value1, $tick2, $value2, $channel, $midiVoice, $bendMode)
  {
    $distance = $value2 - $value1;
    $length = $tick2 - $tick1;
    $points = intval($length / (Duration::QUARTER_TIME / 8));
    for($i = 1; $i <= $points; $i++)
    {
      $tone = (((($length / $points) * $i) * $distance) / $length);
      $bend = self::DEFAULT_BEND + intval($tone * (self::DEFAULT_BEND_SEMI_TONE * 2));
      $this->addBend($sHelper, $track, $tick1 + ( ($length / $points) * $i), $bend, $channel, $midiVoice, $bendMode);
    }
  }

  private function makeFadeIn(MidiSequenceHelper $sHelper, $track, $start, $duration, $volume3, $channel)
  {
    $expression = 31;
    $expressionIncrement = 1;
    $tick = $start;
    $tickIncrement = intval($duration / ((127 - $expression) / $expressionIncrement));
    while($tick < ($start + $duration) && $expression < 127 )
    {
      $sHelper->getSequence()->addControlChange($this->getTick($tick), $track, $channel, MidiWriter::EXPRESSION, $this->fix($expression));
      $tick += $tickIncrement;
      $expression += $expressionIncrement;
    }
    $sHelper->getSequence()->addControlChange($this->getTick(($start + $duration)), $track, $channel, MidiWriter::EXPRESSION, 127);
  }

  private function getStroke(Beat $beat, Beat $previous = null, array $stroke)
  {
    $direction = $beat->getStroke()->getDirection();
    if( $previous === null || !($direction == Stroke::STROKE_NONE && $previous->getStroke()->getDirection() == Stroke::STROKE_NONE))
    {
      if( $direction == Stroke::STROKE_NONE )
      {
        for( $i = 0 ; $i < count($stroke) ; $i++ )
        {
          $stroke[ $i ] = 0;
        }
      }
      else
      {
        $stringUseds = 0;
        $stringCount = 0;
        for( $vIndex = 0; $vIndex < $beat->countVoices(); $vIndex++ )
        {
          $voice = $beat->getVoice($vIndex);
          for ($nIndex = 0; $nIndex < $voice->countNotes(); $nIndex++)
          {
            $note = $voice->getNote($nIndex);
            if( !$note->isTiedNote() )
            {
              $stringUseds |= 0x01 << ( $note->getString() - 1 );
              $stringCount ++;
            }
          }
        }
        if( $stringCount > 0 )
        {
          $strokeMove = 0;
          $strokeIncrement = $beat->getStroke()->getIncrementTime($beat);
          for( $i = 0 ; $i < count($stroke); $i++ )
          {
            $index = ( $direction == Stroke::STROKE_DOWN ? (count($stroke) - 1) - $i : $i );
            if( ($stringUseds & ( 0x01 << $index ) ) != 0 )
            {
              $stroke[ $index ] = $strokeMove;
              $strokeMove += $strokeIncrement;
            }
          }
        }
      }
    }
    return $stroke;
  }

  private function applyStrokeStart( Note $note, $start, array $stroke)
  {
    return ($start + $stroke[ $note->getString() - 1 ]);
  }

  private function applyStrokeDuration( Note $note, $duration, array $stroke)
  {
    return ($duration > $stroke[$note->getString() - 1] ? ($duration - $stroke[ $note->getString() - 1 ]) : $duration );
  }

  private function checkTripletFeel(Voice $voice, $bIndex)
  {
    $bStart = $voice->getBeat()->getStart();
    $bDuration =  $voice->getDuration()->getTime();
    if($voice->getBeat()->getMeasure()->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_EIGHTH)
    {
      if($voice->getDuration()->isEqual($this->newDuration(Duration::EIGHTH)))
      {
        //first time
        if( ($bStart % Duration::QUARTER_TIME) == 0)
        {
          $v = $this->getNextBeat($voice, $bIndex);
          if($v === null || ( $v->getBeat()->getStart() > ($bStart + $voice->getDuration()->getTime()) || $v->getDuration()->isEqual($this->newDuration(Duration::EIGHTH)))  )
          {
            $duration = $this->newDuration(Duration::EIGHTH);
            $duration->getDivision()->setEnters(3);
            $duration->getDivision()->setTimes(2);
            $bDuration = ($duration->getTime() * 2);
          }
        }
        //second time
        else if( ($bStart % (Duration::QUARTER_TIME / 2)) == 0)
        {
          $v = $this->getPreviousBeat($voice, $bIndex);
          if($v === null || ( $v->getBeat()->getStart() < ($bStart - $voice->getDuration()->getTime())  || $v->getDuration()->isEqual($this->newDuration(Duration::EIGHTH)) ))
          {
            $duration = $this->newDuration(Duration::EIGHTH);
            $duration->getDivision()->setEnters(3);
            $duration->getDivision()->setTimes(2);
            $bStart = ( ($bStart - $voice->getDuration()->getTime()) + ($duration->getTime() * 2));
            $bDuration = $duration->getTime();
          }
        }
      }
    }
    else if($voice->getBeat()->getMeasure()->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_SIXTEENTH)
    {
      if($voice->getDuration()->isEqual($this->newDuration(Duration::SIXTEENTH)))
      {
        //first time
        if( ($bStart % (Duration::QUARTER_TIME / 2)) == 0)
        {
          $v = $this->getNextBeat($voice, $bIndex);
          if($v === null || ( $v->getBeat()->getStart() > ($bStart + $voice->getDuration()->getTime()) || $v->getDuration()->isEqual($this->newDuration(Duration::SIXTEENTH)))  )
          {
            $duration = $this->newDuration(Duration::SIXTEENTH);
            $duration->getDivision()->setEnters(3);
            $duration->getDivision()->setTimes(2);
            $bDuration = ($duration->getTime() * 2);
          }
        }
        //second time
        else if( ($bStart % (Duration::QUARTER_TIME / 4)) == 0)
        {
          $v = $this->getPreviousBeat($voice, $bIndex);
          if($v === null || ( $v->getBeat()->getStart() < ($bStart - $voice->getDuration()->getTime())  || $v->getDuration()->isEqual($this->newDuration(Duration::SIXTEENTH)) ))
          {
            $duration = $this->newDuration(Duration::SIXTEENTH);
            $duration->getDivision()->setEnters(3);
            $duration->getDivision()->setTimes(2);
            $bStart = ( ($bStart - $voice->getDuration()->getTime()) + ($duration->getTime() * 2));
            $bDuration = $duration->getTime();
          }
        }
      }
    }
    return new MidiTickHelper($bStart, $bDuration);
  }

  private function newDuration($value)
  {
    $duration = new Duration();
    $duration->setValue($value);
    return $duration;
  }

  private function getPreviousBeat(Voice $beat, $bIndex)
  {
    $previous = null;
    for ($b = $bIndex - 1; $b >= 0; $b--)
    {
      $current = $beat->getBeat()->getMeasure()->getBeat( $b );
      if($current->getStart() < $beat->getBeat()->getStart() && !$current->getVoice($beat->getIndex())->isEmpty())
      {
        if($previous === null || $current->getStart() > $previous->getBeat()->getStart())
        {
          $previous = $current->getVoice($beat->getIndex());
        }
      }
    }
    return $previous;
  }

  private function getNextBeat(Voice $beat, $bIndex)
  {
    $next = null;
    for ($b = $bIndex + 1; $b < $beat->getBeat()->getMeasure()->countBeats(); $b++)
    {
      $current = $beat->getBeat()->getMeasure()->getBeat( $b );
      if($current->getStart() > $beat->getBeat()->getStart() && !$current->getVoice($beat->getIndex())->isEmpty())
      {
        if($next === null || $current->getStart() < $next->getBeat()->getStart())
        {
          $next = $current->getVoice($beat->getIndex());
        }
      }
    }
    return $next;
  }

  private function getNextNote(MidiSequenceHelper $sHelper, Note $note, Track $track, $mIndex, $bIndex, $breakAtRest)
  { 
    $nextBIndex = $bIndex + 1;
    $measureCount = count($sHelper->getMeasureHelpers());
    for ($m = $mIndex; $m < $measureCount; $m++)
    {
      $mHelper = $sHelper->getMeasureHelper( $m );

      $measure = $track->getMeasure( $mHelper->getIndex() );
      $beatCount = $measure->countBeats();
      for ($b = $nextBIndex; $b < $beatCount; $b++)
      {
        $beat = $measure->getBeat( $b );
        $voice = $beat->getVoice( $note->getVoice()->getIndex() );
        if( !$voice->isEmpty() )
        {
          $noteCount = $voice->countNotes();
          for ($n = 0; $n < $noteCount; $n++)
          {
            $nextNote = $voice->getNote( $n );
            if($nextNote->getString() == $note->getString())
            {
              return new MidiNoteHelper($mHelper, $nextNote);
            }
          }
          if( $breakAtRest )
          {
            return null;
          }
        }
      }
      $nextBIndex = 0;
    }

    return null;
  }

  private function getPreviousNote(MidiSequenceHelper $sHelper, Note $note, Track $track, $mIndex, $bIndex, $breakAtRest)
  {
    $nextBIndex = $bIndex;
    for ($m = $mIndex; $m >= 0; $m--)
    {
      $mHelper = $sHelper->getMeasureHelper( $m );

      $measure = $track->getMeasure( $mHelper->getIndex() );
      if( $this->sHeader == -1 || $this->sHeader <= $measure->getNumber() )
      {
        $nextBIndex = $nextBIndex < 0 ? $measure->countBeats() : $nextBIndex;
        for ($b = ($nextBIndex - 1); $b >= 0; $b--)
        {
          $beat = $measure->getBeat( $b );
          $voice = $beat->getVoice( $note->getVoice()->getIndex() );
          if( !$voice->isEmpty() )
          {
            $noteCount = $voice->countNotes();
            for ($n = 0; $n < $noteCount; $n++)
            {
              $current = $voice->getNote( $n );
              if($current->getString() == $note->getString())
              {
                return new MidiNoteHelper($mHelper, $current);
              }
            }
            if( $breakAtRest )
            {
              return null;
            }
          }
        }
      }
      $nextBIndex = -1;
    }

    return null;
  }
}
