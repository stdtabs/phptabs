<?php

namespace PhpTabs\Model;

/**
 * @package Chord
 */

class Chord
{
  private $firstFret;
	private $strings = array();
	private $name;
	private $beat;
	
	public function __construct($length)
  {
		$this->strings = array();
		for($i=0; $i<count($this->strings); $i++)
    {
			$this->strings[$i] = -1;
		}
	}
	
	public function getBeat()
  {
		return $this->beat;
	}
	
	public function setBeat(Beat $beat)
  {
		$this->beat = $beat;
	}
	
	public function addFretValue($string, $fret)
  {
		if($string >= 0 && $string < count($this->strings))
    {
			$this->strings[$string] = $fret;
		}
	}
	
	public function getFretValue($string)
  {
		if($string >= 0 && $string < count($this->strings))
    {
			return $this->strings[$string];
		}
		return -1;
	}
	
	public function getFirstFret()
  {
		return $this->firstFret;
	}
	
	public function setFirstFret($firstFret)
  {
		$this->firstFret = $firstFret;
	}
	
	public function getStrings()
  {
		return $this->strings;
	}
	
	public function countStrings()
  {
		return count($this->strings);
	}
	
	public function countNotes()
  {
		$count = 0;
		for($i = 0; $i<count($this->strings); $i++)
    {
			if($this->strings[$i] >= 0)
      {
				$count++;
			}
		}

		return $count;
	}
	
	public function getName()
  {
		return $this->name;
	}
	
	public function setName($name)
  {
		$this->name = $name;
	}
	
	public function __clone()
  {
		$chord = new Chord(count($this->strings));
		$chord->setName($this->getName());
		$chord->setFirstFret($this->getFirstFret());
		for($i = 0; $i < count($chord->strings); $i++)
    {
			$chord->strings[$i] = $this->strings[$i];
    }
		return $chord;
	}
	
}
