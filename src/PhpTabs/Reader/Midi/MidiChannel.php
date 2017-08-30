<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\Midi;

class MidiChannel
{
  private $channel;
  private $instrument;
  private $volume;
  private $balance;
  private $track;

  /**
   * @param int $channel
   */
  public function __construct($channel)
  {
    $this->channel = $channel;
    $this->instrument = 0;
    $this->volume = 127;
    $this->balance = 64;
    $this->track = -1;
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
   * @return int $channel
   */
  public function getChannel()
  {
    return $this->channel;
  }

  /**
   * @return int $channel
   */
  public function getInstrument()
  {
    return $this->instrument;
  }

  /**
   * @param int $instrument
   */
  public function setInstrument($instrument)
  {
    $this->instrument = $instrument;
  }

  /**
   * @return int $channel
   */
  public function getTrack()
  {
    return $this->track;
  }

  /**
   * @param int $track
   */
  public function setTrack($track)
  {
    $this->track = $track;
  }

  /**
   * @return int $channel
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
}
