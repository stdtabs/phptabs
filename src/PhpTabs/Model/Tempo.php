<?php

namespace PhpTabs\Model;


class Tempo
{
	const SECOND_IN_MILLIS = 1000;
	
	private $value;
	
	public function __construct()
  {
		$this->value = 120;
	}
	
	public function getValue()
  {
		return $this->value;
	}
	
	public function setValue($value)
  {
		$this->value = $value;
	}
	
	public function getInMillis()
  {
		return (60.00 / $this->getValue() * Tempo::SECOND_IN_MILLIS);
	}
	
	public function getInUSQ()
  {
		return ((60.00 / $this->getValue() * Tempo::SECOND_IN_MILLIS) * 1000.00);
	}
	
	public static function fromUSQ($usq)
  {
		$value = ((60.00 * Tempo::SECOND_IN_MILLIS) / ($usq / 1000.00));
		$tempo = new Tempo();
		$tempo->setValue((int)$value);
		return tempo;
	}
	
	public function __clone()
  {
		$tempo = new Tempo();
		$tempo->copyFrom($this);
		return $tempo;
	}
	
	public function copyFrom(Tempo $tempo)
  {
		$this->setValue($tempo->getValue());
	}
}
