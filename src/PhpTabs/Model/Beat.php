<?php

namespace PhpTabs\Model;

/**
 * @package Beat
 */

class Beat
{
	const MAX_VOICES = 2;
	
	private $start;
	private $measure;
	private $chord;
	private $text;
	private $voices;
	private $stroke;
	
	public function __construct()
  {
		$this->start = Duration::QUARTER_TIME;
		$this->stroke = new Stroke();
		$this->voices = array();
		for ($i = 0; $i < Beat::MAX_VOICES; $i++)
			$this->setVoice($i, new Voice($i));
	}
	
	public function getMeasure()
  {
		return $this->measure;
	}
	
	public function setMeasure(Measure $measure)
  {
		$this->measure = $measure;
	}
	
	public function getStart()
  {
		return $this->start;
	}
	
	public function setStart($start)
  {
		$this->start = $start;
	}
	
	public function setVoice($index, Voice $voice)
  {
		if($index >= 0)
    {
			$this->voices[$index] = $voice;
			$this->voices[$index]->setBeat($this);
		}
	}
	
	public function getVoice($index)
  {
		if($index >= 0 && $index < count($this->voices))
    {
			return $this->voices[$index];
		}
		return null;
	}
	
	public function countVoices()
  {
		return count($this->voices);
	}
	
	public function setChord(Chord $chord)
  {
		$this->chord = $chord;
		$this->chord->setBeat($this);
	}
	
	public function getChord()
  {
		return $this->chord;
	}
	
	public function removeChord()
  {
		$this->chord = null;
	}
	
	public function getText()
  {
		return $this->text;
	}
	
	public function setText(Text $text)
  {
		$this->text = $text;
		$this->text->setBeat($this);
	}
	
	public function removeText()
  {
		$this->text = null;
	}
	
	public function isChordBeat()
  {
		return $this->chord != null;
	}
	
	public function isTextBeat()
  {
		return $this->text != null;
	}
	
	public function getStroke()
  {
		return $this->stroke;
	}
	
	public function isRestBeat()
  {
		for($v = 0; $v < $this->countVoices(); $v++)
    {
			$voice = $this->getVoice($v);
			if(!$voice->isEmpty() && !$voice->isRestVoice())
      {
				return false;
			}
		}

		return true;
	}
	
	public function __clone()
  {
		$beat = new Beat();
		$beat->setStart($this->getStart());
		$beat->getStroke()->copyFrom($this->getStroke());
		for ($i = 0; $i < count($this->voices); $i++)
    {
			$beat->setVoice($i, clone $this->voices[$i]);
		}
		if($this->chord != null)
			$beat->setChord(clone $this->chord);
		
		if($this->text != null)
			$beat->setText(clone $this->text);
		
		return $beat;
	}
}
