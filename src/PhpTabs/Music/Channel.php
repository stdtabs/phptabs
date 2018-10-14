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

class Channel
{
  const DEFAULT_PERCUSSION_CHANNEL = 9;
  const DEFAULT_PERCUSSION_PROGRAM = 0;
  const DEFAULT_PERCUSSION_BANK    = 128;

  const DEFAULT_BANK    = 0;
  const DEFAULT_PROGRAM = 25;
  const DEFAULT_VOLUME  = 127;
  const DEFAULT_BALANCE = 64;
  const DEFAULT_CHORUS  = 0;
  const DEFAULT_REVERB  = 0;
  const DEFAULT_PHASER  = 0;
  const DEFAULT_TREMOLO = 0;

  private $channelId  = 0;
  private $name       = '';
  private $parameters = [];
  private $bank;
  private $program;
  private $volume;
  private $balance;
  private $chorus;
  private $reverb;
  private $phaser;
  private $tremolo;

  public function __construct()
  {
    $this->bank    = Channel::DEFAULT_BANK;
    $this->program = Channel::DEFAULT_PROGRAM;
    $this->volume  = Channel::DEFAULT_VOLUME;
    $this->balance = Channel::DEFAULT_BALANCE;
    $this->chorus  = Channel::DEFAULT_CHORUS;
    $this->reverb  = Channel::DEFAULT_REVERB;
    $this->phaser  = Channel::DEFAULT_PHASER;
    $this->tremolo = Channel::DEFAULT_TREMOLO;
  }

  /**
   * @return int
   */
  public function getChannelId()
  {
    return $this->channelId;
  }

  /**
   * @param int $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }

  /**
   * @return int
   */
  public function getBalance()
  {
    return $this->balance;
  }

  /**
   * @param int $balance
   */
  public function setBalance($balance)
  {
    $this->balance = $balance;
  }

  /**
   * @return int
   */
  public function getChorus()
  {
    return $this->chorus;
  }

  /**
   * @param int $chorus
   */
  public function setChorus($chorus)
  {
    $this->chorus = $chorus;
  }

  /**
   * @return int
   */
  public function getBank()
  {
    return $this->bank;
  }

  /**
   * @param int $bank
   */
  public function setBank($bank)
  {
    $this->bank = $bank;
  }

  /**
   * @return int
   */
  public function getProgram()
  {
    return $this->program;
  }

  /**
   * @param int $program
   */
  public function setProgram($program)
  {
    $this->program = $program;
  }

  /**
   * @return int
   */
  public function getPhaser()
  {
    return $this->phaser;
  }

  /**
   * @param int $phaser
   */
  public function setPhaser($phaser)
  {
    $this->phaser = $phaser;
  }

  /**
   * @return int
   */
  public function getReverb()
  {
    return $this->reverb;
  }

  /**
   * @param int $reverb
   */
  public function setReverb($reverb)
  {
    $this->reverb = $reverb;
  }

  /**
   * @return int
   */
  public function getTremolo()
  {
    return $this->tremolo;
  }

  /**
   * @param int $tremolo
   */
  public function setTremolo($tremolo)
  {
    $this->tremolo = $tremolo;
  }

  /**
   * @return int
   */
  public function getVolume()
  {
    return $this->volume;
  }

  /**
   * @param int $volume
   */
  public function setVolume($volume)
  {
    $this->volume = $volume;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return array
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * @param \PhpTabs\Music\ChannelParameter $parameter
   */
  public function addParameter(ChannelParameter $parameter)
  {
    $this->parameters[] = $parameter;
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\ChannelParameter $parameter
   */
  public function setParameter($index, ChannelParameter $parameter)
  {
    $this->parameters[$index] = $parameter;
  }

  /**
   * @param  int $index
   * @return \PhpTabs\Music\ChannelParameter
   */
  public function getParameter($index)
  {
    return isset($this->parameters[$index])
         ? $this->parameters[$index] : null;
  }

  /**
   * @param int $index
   */
  public function removeParameter($index)
  {
    array_splice($this->parameters, $index, 1);
  }

  /**
   * @return int
   */
  public function countParameters()
  {
    return count($this->parameters);
  }

  /**
   * @return bool
   */
  public function isPercussionChannel()
  {
    return $this->getBank() === Channel::DEFAULT_PERCUSSION_BANK;
  }

  /**
   * @return \PhpTabs\Music\Channel
   */
  public function __clone()
  {
    $channel = new Channel();
    $channel->copyFrom($this);
    return $channel; 
  }

  /**
   * @param \PhpTabs\Music\Channel $channel
   */
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

    for ($i = 0; $i < $channel->countParameters(); $i++)
    {
      $this->addParameter(clone $channel->getParameter($i));
    }
  }
}
