<?php

namespace PhpTabs\Model;


class Channel
{
	const DEFAULT_PERCUSSION_CHANNEL = 9;
	const DEFAULT_PERCUSSION_PROGRAM = 0;
	const DEFAULT_PERCUSSION_BANK = 128;
	
	const DEFAULT_BANK = 0;
	const DEFAULT_PROGRAM = 25;
	const DEFAULT_VOLUME = 127;
	const DEFAULT_BALANCE = 64;
	const DEFAULT_CHORUS = 0;
	const DEFAULT_REVERB = 0;
	const DEFAULT_PHASER = 0;
	const DEFAULT_TREMOLO = 0;
	
	private $channelId;
	private $bank;
	private $program;
	private $volume;
	private $balance;
	private $chorus;
	private $reverb;
	private $phaser;
	private $tremolo;
	private $name;
	private $parameters = array();
	
	public function __construct()
  {
		$this->channelId = 0;
		$this->bank = Channel::DEFAULT_BANK;
		$this->program = Channel::DEFAULT_PROGRAM;
		$this->volume = Channel::DEFAULT_VOLUME;
		$this->balance = Channel::DEFAULT_BALANCE;
		$this->chorus = Channel::DEFAULT_CHORUS;
		$this->reverb = Channel::DEFAULT_REVERB;
		$this->phaser = Channel::DEFAULT_PHASER;
		$this->tremolo = Channel::DEFAULT_TREMOLO;
		$this->name = '';
		$this->parameters = array();
	}
	
	public function getChannelId()
  {
		return $this->channelId;
	}
	
	public function setChannelId($channelId)
  {
		$this->channelId = $channelId;
	}
	
	public function getBalance()
  {
		return $this->balance;
	}
	
	public function setBalance($balance)
  {
		$this->balance = $balance;
	}
	
	public function getChorus()
  {
		return $this->chorus;
	}
	
	public function setChorus($chorus)
   {
		$this->chorus = $chorus;
	}
	
	public function getBank()
  {
		return $this->bank;
	}
	
	public function setBank($bank)
  {
		$this->bank = $bank;
	}
	
	public function getProgram()
  {
		return $this->program;
	}
	
	public function setProgram($program)
  {
		$this->program = $program;
	}
	
	public function getPhaser()
  {
		return $this->phaser;
	}
	
	public function setPhaser($phaser)
  {
		$this->phaser = $phaser;
	}
	
	public function getReverb()
  {
		return $this->reverb;
	}
	
	public function setReverb($reverb)
  {
		$this->reverb = $reverb;
	}
	
	public function getTremolo()
  {
		return $this->tremolo;
	}
	
	public function setTremolo($tremolo)
  {
		$this->tremolo = $tremolo;
	}
	
	public function getVolume()
  {
		return $this->volume;
	}
	
	public function setVolume($volume)
  {
		$this->volume = $volume;
	}
	
	public function getName()
  {
		return $this->name;
	}
	
	public function setName($name)
  {
		$this->name = $name;
	}
	
	public function getParameters()
  {
		return $this->parameters;
	}
	
	public function addParameter(ChannelParameter $parameter)
  {
		$this->parameters[] = $parameter;
	}
	
	public function setParameter($index, ChannelParameter $parameter)
  {
		$this->parameters[$index] = $parameter;
	}
	
	public function getParameter($index)
  {
		if($index >= 0 && $index < $this->countParameters())
    {
			return $this->parameters[$index];
		}
		return null;
	}
	
	public function removeParameter($index)
  {
		array_splice($this->parameters, $index, 1);
	}
	
	public function countParameters()
  {
		return count($this->parameters);
	}
	
	public function isPercussionChannel()
  {
		return ($this->getBank() == Channel::DEFAULT_PERCUSSION_BANK);
	}
	
	public function __clone()
  {
		$channel = new Channel();
		$channel->copyFrom($this);
		return $channel; 
	}
	
	public function copyFrom(Channel $channel)
  {
		$this->setChannelId($channel->getChannelId());
		$this->setBank($channel->getBank());
		$this->setProgram($channel->getProgram());
		$this->setVolume($channel->getVolume());
		$this->setBalance($channel->getBalance());
		$this->setChorus($channel->getChorus());
		$this->setReverb($channel->getReverb());
		$this->setPhaser($channel->getPhaser());
		$this->setTremolo($channel->getTremolo());
		$this->setName($channel->getName());

    $this->parameters = array(); 

    for($i = 0; $i < $channel->countParameters(); $i++)
    {
			$this->addParameter(clone $channel->getParameter($i));
		}
	}
}
