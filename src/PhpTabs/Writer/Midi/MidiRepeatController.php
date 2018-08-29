<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Midi;

use PhpTabs\Music\Duration;
use PhpTabs\Music\Song;

class MidiRepeatController
{
  private $song;
  private $count;
  private $index;
  private $lastIndex;	
  private $shouldPlay;	
  private $repeatOpen;
  private $repeatStart;
  private $repeatEnd;
  private $repeatMove;
  private $repeatStartIndex;
  private $repeatNumber;
  private $repeatAlternative;
  private $sHeader;
  private $eHeader;

  /**
   * @param \PhpTabs\Music\Song $song
   * @param mixed $sHeader
   * @param mixed $eHeader
   */
  public function __construct(Song $song, $sHeader, $eHeader)
  {
    $this->song = $song;
    $this->sHeader = $sHeader;
    $this->eHeader = $eHeader;
    $this->count = $song->countMeasureHeaders();
    $this->index = 0;
    $this->lastIndex = -1;
    $this->shouldPlay = true;
    $this->repeatOpen = true;
    $this->repeatAlternative = 0;
    $this->repeatStart = Duration::QUARTER_TIME;
    $this->repeatEnd = 0;
    $this->repeatMove = 0;
    $this->repeatStartIndex = 0;
    $this->repeatNumber = 0;
  }

  public function process()
  {
    $header = $this->song->getMeasureHeader($this->index);

    // Checks pointer is in range
    if (($this->sHeader != -1 && $header->getNumber() < $this->sHeader) || ($this->eHeader != -1 && $header->getNumber() > $this->eHeader))
    {
      $this->shouldPlay = false;
      $this->index ++;
      return;
    }

    // always repeat open first
    if (($this->sHeader != -1 && $header->getNumber() == $this->sHeader ) || $header->getNumber() == 1)
    {
      $this->repeatStartIndex = $this->index;
      $this->repeatStart = $header->getStart();
      $this->repeatOpen = true;
    }

    // By default, should sound
    $this->shouldPlay = true;

    // If repeat open, memorize on which measure it starts
    if ($header->isRepeatOpen())
    {
      $this->repeatStartIndex = $this->index;
      $this->repeatStart = $header->getStart();
      $this->repeatOpen = true;

      // First pass on the repeat
      if ($this->index > $this->lastIndex)
      {
        $this->repeatNumber = 0;
        $this->repeatAlternative = 0;
      }
    }
    else
    {
      // Checks if an alternative has been opened
      if ($this->repeatAlternative == 0)
      {
        $this->repeatAlternative = $header->getRepeatAlternative();
      }
      // Final alternative
      if ($this->repeatOpen && ($this->repeatAlternative > 0) && (($this->repeatAlternative & (1 << ($this->repeatNumber))) == 0))
      {
        $this->repeatMove -= $header->getLength();

        if ($header->getRepeatClose() > 0)
        {
          $this->repeatAlternative = 0;
        }

        $this->shouldPlay = false;
        $this->index ++;
        return;
      }
    }

    // Before executing a repeat, keep index of last one
    $this->lastIndex = max($this->lastIndex, $this->index);

    // If repeat, pass through
    if ($this->repeatOpen && $header->getRepeatClose() > 0)
    {
      if ($this->repeatNumber < $header->getRepeatClose() || ($this->repeatAlternative > 0))
      {
        $this->repeatEnd = $header->getStart() + $header->getLength();
        $this->repeatMove += $this->repeatEnd - $this->repeatStart;
        $this->index = $this->repeatStartIndex - 1;
        $this->repeatNumber++;
      }
      else
      {
        $this->repeatStart = 0;
        $this->repeatNumber = 0;
        $this->repeatEnd = 0;
        $this->repeatOpen = false;
      }

      $this->repeatAlternative = 0;
    }

    $this->index++;
  }

  /**
   * @return bool
   */
  public function finished()
  {
    return ($this->index >= $this->count);
  }

  /**
   * @return bool
   */
  public function shouldPlay()
  {
    return $this->shouldPlay;
  }

  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }

  /**
   * @return int
   */
  public function getRepeatMove()
  {
    return $this->repeatMove;
  }
}
