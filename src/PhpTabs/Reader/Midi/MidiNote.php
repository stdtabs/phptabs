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

class MidiNote
{
  private $track;
  private $channel;
  private $tick;
  private $value;
  private $velocity;
  private $pitchBends;

  /**
   * @param int $track
   * @param int $track
   * @param int $tick
   * @param int $value
   * @param int $velocity
   */
  public function __construct($track, $channel, $tick, $value, $velocity)
  {
    $this->track = $track;
    $this->channel = $channel;
    $this->tick = $tick;
    $this->value = $value;
    $this->velocity = $velocity;
    $this->pitchBends = array();
  }

  /**
   * @return int
   */
  public function getChannel()
  {
    return $this->channel;
  }

  /**
   * @return int
   */
  public function getTick()
  {
    return $this->tick;
  }

  /**
   * @return int
   */
  public function getTrack()
  {
    return $this->track;
  }

  /**
   * @return int
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @return int
   */
  public function getVelocity()
  {
    return $this->velocity;
  }

  /**
   * @param int $value
   */
  public function addPitchBend($value)
  {
    $this->pitchBends[] = $value;
  }

  /**
   * @return array
   */
  public function getPitchBends()
  {
    return $this->pitchBends;
  }

  /**
   * @return int
   */
  public function countPitchBends()
  {
    return count($this->pitchBends);
  }
}
