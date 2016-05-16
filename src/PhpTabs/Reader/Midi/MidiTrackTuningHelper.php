<?php

namespace PhpTabs\Reader\Midi;

use PhpTabs\Model\TabString;

class MidiTrackTuningHelper
{
  private $track;
  private $maxValue;
  private $minValue;

  public function __construct($track)
  {
    $this->track = $track;
    $this->maxValue = -1;
    $this->minValue = -1;
  }

  public function checkValue($value)
  {
    if($this->minValue < 0 || $value < $this->minValue)
    {
      $this->minValue = $value;
    }
    if($this->maxValue < 0 || $value > $this->maxValue)
    {
      $this->maxValue = $value;
    }
  }

  public function getStrings()
  {
    $strings = array();

    $maxFret = 24;

    if($this->minValue >= 40 && $this->maxValue <= 64 + $maxFret)
    {
      $strings[] = new TabString(1, 64);
      $strings[] = new TabString(2, 59);
      $strings[] = new TabString(3, 55);
      $strings[] = new TabString(4, 50);
      $strings[] = new TabString(5, 45);
      $strings[] = new TabString(6, 40);
    }
    else if($this->minValue >= 38 && $this->maxValue <= 64 + $maxFret)
    {
      $strings[] = new TabString(1, 64);
      $strings[] = new TabString(2, 59);
      $strings[] = new TabString(3, 55);
      $strings[] = new TabString(4, 50);
      $strings[] = new TabString(5, 45);
      $strings[] = new TabString(6, 38);
    }
    else if($this->minValue >= 35 && $this->maxValue <= 64 + $maxFret)
    {
      $strings[] = new TabString(1, 64);
      $strings[] = new TabString(2, 59);
      $strings[] = new TabString(3, 55);
      $strings[] = new TabString(4, 50);
      $strings[] = new TabString(5, 45);
      $strings[] = new TabString(6, 40);
      $strings[] = new TabString(7, 35);
    }
    else if($this->minValue >= 28 && $this->maxValue <= 43 + $maxFret)
    {
      $strings[] = new TabString(1, 43);
      $strings[] = new TabString(2, 38);
      $strings[] = new TabString(3, 33);
      $strings[] = new TabString(4, 28);
    }
    else if($this->minValue >= 23 && $this->maxValue <= 43 + $maxFret)
    {
      $strings[] = new TabString(1, 43);
      $strings[] = new TabString(2, 38);
      $strings[] = new TabString(3, 33);
      $strings[] = new TabString(4, 28);
      $strings[] = new TabString(5, 23);
    }
    else
    {
      $stringCount = 6;
      $stringSpacing = (($this->maxValue - ($maxFret - 4) - $this->minValue) / $stringCount);
      if($stringSpacing > 5)
      {
        $stringCount = 7;
        $stringSpacing = (($this->maxValue - ($maxFret - 4) - $this->minValue) / $stringCount);
      }

      $maxStringValue = ($this->minValue + ($stringCount * $stringSpacing));
      while(count($strings) < $stringCount)
      {
        $maxStringValue -= $stringSpacing;
        $strings[] = new TabString(count($strings) + 1, $maxStringValue);
      }
    }

    return $strings;
  }

  public function getTrack()
  {
    return $this->track;
  }
}
