<?php

namespace PhpTabs\Model;

/**
 * @uses NoteEffect
 * @uses Velocities
 * @uses Voice
 */
class Note
{
  private $value;
  private $velocity;
  private $string;
  private $tiedNote;
  private $effect;
  private $voice;

  public function __construct()
  {
    $this->value = 0;
    $this->velocity = Velocities::FORTE;
    $this->string = 1;
    $this->tiedNote = false;
    $this->effect = new NoteEffect();
  }

  /**
   * @return int
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * @return int
   */
  public function getVelocity()
  {
    return $this->velocity;
  }

  /**
   * @param int $velocity
   */
  public function setVelocity($velocity)
  {
    $this->velocity = $velocity;
  }

  /**
   * @return \PhpTabs\Model\TabString
   */
  public function getString()
  {
    return $this->string;
  }

  /**
   * @param \PhpTabs\Model\TabString $string
   */
  public function setString($string)
  {
    $this->string = $string;
  }

  /**
   * @return bool
   */
  public function isTiedNote()
  {
    return $this->tiedNote;
  }

  /**
   * @param bool $tiedNote
   */
  public function setTiedNote($tiedNote)
  {
    $this->tiedNote = $tiedNote;
  }

  /**
   * @return \PhpTabs\Model\NoteEffect
   */
  public function getEffect()
  {
    return $this->effect;
  }

  /**
   * @param \PhpTabs\Model\NoteEffect $effect
   */
  public function setEffect(NoteEffect $effect)
  {
    $this->effect = $effect;
  }

  /**
   * @return \PhpTabs\Model\Voice
   */
  public function getVoice()
  {
    return $this->voice;
  }

  /**
   * @param \PhpTabs\Model\Voice $voice
   */
  public function setVoice(Voice $voice)
  {
    $this->voice = $voice;
  }

  /**
   * @return \PhpTabs\Model\Note
   */
  public function __clone()
  {
    $note = new Note();
    $note->setValue($this->getValue());
    $note->setVelocity($this->getVelocity());
    $note->setString($this->getString());
    $note->setTiedNote($this->isTiedNote());
    $note->setEffect(clone $this->getEffect());

    return $note;
  }
}
