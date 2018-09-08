<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

/**
 * @uses Beat
 * @uses Duration
 * @uses Note
 */
class Voice
{
  const DIRECTION_NONE = 0;
  const DIRECTION_UP   = 1;
  const DIRECTION_DOWN = 2;

  private $beat;
  private $duration;
  private $index;
  private $direction;
  private $notes = [];
  private $empty = true;

  /**
   * @param int $index
   */
  public function __construct($index)
  {
    $this->duration  = new Duration();
    $this->index     = $index;
    $this->direction = Voice::DIRECTION_NONE;
  }

  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }

  /**
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
    return $this->empty;
  }

  /**
   * @param bool $empty
   */
  public function setEmpty($empty)
  {
    $this->empty = $empty;
  }

  /**
   * @return int
   */
  public function getDirection()
  {
    return $this->direction;
  }

  /**
   * @param int $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }

  /**
   * @return \PhpTabs\Music\Duration
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * @param \PhpTabs\Music\Duration $duration
   */
  public function setDuration(Duration $duration)
  {
    $this->duration = $duration;
  }

  /**
   * @return \PhpTabs\Music\Beat
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
   */
  public function setBeat(Beat $beat)
  {
    $this->beat = $beat;
  }

  /**
   * @return array
   */
  public function getNotes()
  {
    return $this->notes;
  }

  /**
   * @param \PhpTabs\Music\Note $note
   */
  public function addNote(Note $note)
  {
    $note->setVoice($this);
    $this->notes[] = $note;
    $this->setEmpty(false);
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\Note $note
   */
  public function moveNote($index, Note $note)
  {
    $this->removeNote($note);

    array_splice($this->notes, $index, 0, $note);
  }

  /**
   * @param \PhpTabs\Music\Note $note
   */
  public function removeNote(Note $note)
  {
    foreach ($this->notes as $k => $v)
    {
      if ($v == $note)
      {
        array_splice($this->notes, $k, 1);

        if (!$this->countNotes())
        {
          $this->setEmpty(true);
        }

        return;
      }
    }
  }

  /**
   * @param int $index
   *
   * @return \PhpTabs\Music\Note
   */
  public function getNote($index)
  {
    return isset($this->notes[$index])
         ? $this->notes[$index] : null;
  }

  /**
   * @return int
   */
  public function countNotes()
  {
    return count($this->notes);
  }

  /**
   * @return bool
   */
  public function isRestVoice()
  {
    return count($this->notes) == 0;
  }

  /**
   * Get duration in seconds
   * 
   * @return float
   */
  public function getTime()
  {
	$measure = $this->getBeat()->getMeasure();

    $time = 60 
      * $measure->getTimeSignature()->getNumerator()
      / $measure->getTempo()->getValue();

    return $time 
         * $this->getDuration()->getTime()
         / $this->getMeasureDuration($measure); 
  }

  /**
   * Calculate total measure duration
   * 
   * @param  \PhpTabs\Music\Measure $measure
   * @return int
   */
  private function getMeasureDuration(Measure $measure)
  {
    return array_reduce(
      $measure->getBeats(),
      $this->getMeasureTimeHelper(),
      0
    );
  }

  /**
   * Provides a closure helper for measure time calculation
   * 
   * @return \Closure
   */
  private function getMeasureTimeHelper()
  {
    return function ($carry, $item) {
      return $carry
           + $item
              ->getVoice($this->getIndex())
              ->getDuration()
              ->getTime();
    };
  }

  /**
   * @return \PhpTabs\Music\Voice
   */
  public function __clone()
  {
    $voice = new Voice($this->getIndex());
    $voice->setEmpty($this->isEmpty());
    $voice->setDirection($this->getDirection());
    $voice->getDuration()->copyFrom($this->getDuration());

    for ($i = 0; $i < $this->countNotes(); $i++)
    {
      $note = $this->notes[$i];

      $voice->addNote(clone $note);
    }

    return $voice;
  }
}
