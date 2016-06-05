<?php

namespace PhpTabs\Reader\Midi;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Measure;
use PhpTabs\Model\Song;
use PhpTabs\Model\Track;

class MidiAdjuster
{
  private $minDurationTime;
  private $song;

  public function __construct(Song $song)
  {
    $this->song = $song;
    $this->minDurationTime = 40;
  }
	
  public function adjustSong()
  {
    $tracks = $this->song->getTracks();

    foreach($tracks as $track)
    {
      $this->adjustTrack($track);
    }

    return $this->song;
  }
	
  private function adjustTrack(Track $track)
  {
    $measures = $track->getMeasures();

    foreach($measures as $measure)
    {
      $this->process($measure);
    }
  }

  private function process(Measure $measure)
  {
    $this->orderBeats($measure);
    $this->joinBeats($measure);
    $this->adjustBeatsStrings($measure);
  }

  private function joinBeats(Measure $measure)
  {
    $previous = null;
    $finish = true;

    $measureStart = $measure->getStart();
    $measureEnd = $measureStart + $measure->getLength();
    
    for($i = 0; $i < $measure->countBeats(); $i++)
    {
      $beat = $measure->getBeat($i);
      $beatStart = $beat->getStart();
      $beatLength = $beat->getVoice(0)->getDuration()->getTime();

      if($previous !== null)
      {
        $previousStart = $previous->getStart();
        $previousLength = $previous->getVoice(0)->getDuration()->getTime();

        if($beatStart >= $previousStart && ($previousStart + $this->minDurationTime) > $beatStart)
        {
          // add beat notes to previous
          for($n = 0; $n < $beat->getVoice(0)->countNotes(); $n++)
          {
            $note = $beat->getVoice(0)->getNote($n);
            $previous->getVoice(0)->addNote($note);
          }

          // add beat chord to previous
          if(!$previous->isChordBeat() && $beat->isChordBeat())
          {
            $previous->setChord( $beat->getChord() );
          }

          // add beat text to previous
          if(!$previous->isTextBeat() && $beat->isTextBeat())
          {
            $previous->setText( $beat->getText() );
          }

          // set the best duration
          if($beatLength > $previousLength && ($beatStart + $beatLength) <= $measureEnd)
          {
            $previous->getVoice(0)->getDuration()->copyFrom($beat->getVoice(0)->getDuration());
          }

          $measure->removeBeat($beat);
          $finish = false;
          break;
        }
        else if($previousStart < $beatStart && ($previousStart + $previousLength) > $beatStart)
        {
          if($beat->getVoice(0)->isRestVoice())
          {
            $measure->removeBeat($beat);
            $finish = false;
            break;
          }

          $duration = Duration::fromTime($beatStart - $previousStart);

          $previous->getVoice(0)->getDuration()->copyFrom($duration);
        }
      }
      if( ($beatStart + $beatLength) > $measureEnd )
      {
        if($beat->getVoice(0)->isRestVoice())
        {
          $measure->removeBeat($beat);
          $finish = false;
          break;
        }

        $duration = Duration::fromTime($measureEnd - $beatStart);

        $beat->getVoice(0)->getDuration()->copyFrom($duration);
      }

      $previous = $beat;
    }

    if(!$finish)
    {
      $this->joinBeats($measure);
    }
  }

  private function orderBeats(Measure $measure)
  {
    for($i = 0; $i < $measure->countBeats(); $i++)
    {
      $minBeat = null;

      for($j = $i; $j < $measure->countBeats(); $j++)
      {
        $beat = $measure->getBeat($j);

        if($minBeat === null || $beat->getStart() < $minBeat->getStart())
        {
          $minBeat = $beat;
        }
      }

      $measure->moveBeat($i, $minBeat);
    }
  }

  private function adjustBeatsStrings(Measure $measure)
  {
    for($i = 0; $i < $measure->countBeats(); $i++)
    {
      $beat = $measure->getBeat($i);

      $this->adjustStrings($beat);
    }
  }

  private function adjustStrings(Beat $beat)
  {
    $track = $beat->getMeasure()->getTrack();
    $freeStrings = $track->getStrings();
    $notesToRemove = array();

    $notes = $beat->getVoice(0)->getNotes();

    foreach($notes as $note)
    {
      $string = $this->getStringForValue($freeStrings, $note->getValue());
      
      for($j = 0; $j < count($freeStrings); $j++)
      {
        $tempString = $freeStrings[$j];

        if($tempString->getNumber() == $string)
        {
          $note->setValue($note->getValue() - $tempString->getValue());
          $note->setString($tempString->getNumber());

          array_splice($freeStrings, $j, 1);
          break;
        }
      }

      //Cannot have more notes on same string 
      if($note->getString() < 1)
      {
        $notesToRemove[] = $note;
      }
    }

    // Remove notes
    while(count($notesToRemove) > 0)
    {
      $beat->getVoice(0)->removeNote($notesToRemove[0]);

      array_splice($notesToRemove, 0, 1);
    }
  }

  private function getStringForValue($strings, $value)
  {
    $minFret = -1;
    $stringForValue = 0;

    for($i = 0; $i < count($strings); $i++)
    {
      $string = $strings[$i];
      $fret = $value - $string->getValue();

      if($minFret < 0 || ($fret >= 0 && $fret < $minFret))
      {
        $stringForValue = $string->getNumber();

        $minFret = $fret;
      }
    }

    return $stringForValue;
  }
}
