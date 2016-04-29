<?php

namespace PhpTabs\Model;

/**
 * @package Note
 * @uses NoteEffect
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
	
	public function getValue()
  {
		return $this->value;
	}
	
	public function setValue($value)
  {
		$this->value = $value;
	}
	
	public function getVelocity()
  {
		return $this->velocity;
	}
	
	public function setVelocity($velocity)
  {
		$this->velocity = $velocity;
	}
	
	public function getString()
  {
		return $this->string;
	}
	
	public function setString($string)
  {
		$this->string = $string;
	}
	
	public function isTiedNote()
  {
		return $this->tiedNote;
	}
	
	public function setTiedNote($tiedNote)
  {
		$this->tiedNote = $tiedNote;
	}
	
	public function getEffect()
  {
		return $this->effect;
	}
	
	public function setEffect(NoteEffect $effect)
  {
		$this->effect = $effect;
	}
	
	public function getVoice()
  {
		return $this->voice;
	}
	
	public function setVoice(Voice $voice)
  {
		$this->voice = $voice;
	}
	
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
