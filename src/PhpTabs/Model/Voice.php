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

  public function __construct($index)
  {
    $this->duration = new Duration();
    $this->notes = array();
    $this->index = $index;
    $this->empty = true;
    $this->direction = Voice::DIRECTION_NONE;
  }

  public function getIndex()
  {
    return $this->index;
  }

  public function setIndex($index)
  {
    $this->index = $index;
  }

  public function isEmpty()
  {
    return $this->empty;
  }

  public function setEmpty($empty)
  {
    $this->empty = $empty;
  }

  public function getDirection()
  {
    return $this->direction;
  }

  public function setDirection($direction)
  {
    $this->direction = $direction;
  }

  public function getDuration()
  {
    return $this->duration;
  }

  public function setDuration(Duration $duration)
  {
    $this->duration = $duration;
  }

  public function getBeat()
  {
    return $this->beat;
  }

  public function setBeat(Beat $beat)
  {
    $this->beat = $beat;
  }

  public function getNotes()
  {
    return $this->notes;
  }

  public function addNote(Note $note)
  {
    $note->setVoice($this);
    $this->notes[] = $note;
    $this->setEmpty(false);
  }

  public function moveNote($index, Note $note)
  {
    $this->removeNote($note);

    array_splice($this->notes, $index, 0, $note);
  }

  public function removeNote(Note $note)
  {
    foreach($this->notes as $k => $v)
      if($v == $note)
        array_splice($this->notes, $k, 1);
  }

  public function getNote($index)
  {
    if($index >= 0 && $index < $this->countNotes())
    {
      return $this->notes[$index];
    }
    return null;
  }

  public function countNotes()
  {
    return count($this->notes);
  }

  public function isRestVoice()
  {
    return count($this->notes) == 0;
  }

  public function __clone()
  {
    $voice = new Voice($this->getIndex());
    $voice->setEmpty($this->isEmpty());
    $voice->setDirection($this->getDirection());
    $voice->getDuration()->copyFrom($this->getDuration());

    for($i=0; $i<$this->countNotes(); $i++)
    {
      $note = $this->notes[$i];
      $voice->addNote(clone $note);
    }

    return $voice;
  }
}
