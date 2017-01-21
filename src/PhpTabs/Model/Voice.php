<?php

namespace PhpTabs\Model;

/**
 * @uses Beat
 * @uses Duration
 * @uses Note
 */
class Voice
{
  const DIRECTION_NONE = 0;
  const DIRECTION_UP = 1;
  const DIRECTION_DOWN = 2;

  private $beat;
  private $duration;
  private $notes;
  private $index;
  private $direction;
  private $empty;

  /**
   * @param int $index
   */
  public function __construct($index)
  {
    $this->duration = new Duration();
    $this->notes = array();
    $this->index = $index;
    $this->empty = true;
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
   * @return int
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * @param \PhpTabs\Model\Duration $duration
   */
  public function setDuration(Duration $duration)
  {
    $this->duration = $duration;
  }

  /**
   * @return \PhpTabs\Model\Beat
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param \PhpTabs\Model\Beat $beat
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
   * @param \PhpTabs\Model\Note $note
   */
  public function addNote(Note $note)
  {
    $note->setVoice($this);
    $this->notes[] = $note;
    $this->setEmpty(false);
  }

  /**
   * @param int $index
   * @param \PhpTabs\Model\Note $note
   */
  public function moveNote($index, Note $note)
  {
    $this->removeNote($note);

    array_splice($this->notes, $index, 0, $note);
  }

  /**
   * @param \PhpTabs\Model\Note $note
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
   * @return \PhpTabs\Model\Note
   */
  public function getNote($index)
  {
    if ($index >= 0 && $index < $this->countNotes())
    {
      return $this->notes[$index];
    }

    return null;
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
   * @return \PhpTabs\Model\Voice
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
