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
 * @uses NoteEffect
 * @uses Velocities
 * @uses Voice
 */
class Note
{
  private $value    = 0;
  private $string   = 1;
  private $tiedNote = false;
  private $velocity;
  private $effect;
  private $voice;

  public function __construct()
  {
    $this->velocity = Velocities::FORTE;
    $this->effect   = new NoteEffect();
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
   * @return int
   */
  public function getString()
  {
    return $this->string;
  }

  /**
   * @param int $string
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
   * @return \PhpTabs\Music\NoteEffect
   */
  public function getEffect()
  {
    return $this->effect;
  }

  /**
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  public function setEffect(NoteEffect $effect)
  {
    $this->effect = $effect;
  }

  /**
   * @return \PhpTabs\Music\Voice
   */
  public function getVoice()
  {
    return $this->voice;
  }

  /**
   * @param \PhpTabs\Music\Voice $voice
   */
  public function setVoice(Voice $voice)
  {
    $this->voice = $voice;
  }

  /**
   * @return \PhpTabs\Music\Note
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
