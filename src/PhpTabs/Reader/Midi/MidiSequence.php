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

class MidiSequence
{
  /** Sequence */
  const PPQ = 0.0;
  const SMPTE_24 = 24.0;
  const SMPTE_25 = 25.0;
  const SMPTE_30DROP = 29.97;
  const SMPTE_30 = 30.0;

  protected $divisionType;
  protected $resolution;
  private $tracks;

  /**
   * @param mixed $divisionType
   * @param mixed $resolution
   */
  public function __construct($divisionType, $resolution)
  {
    $this->divisionType = $divisionType;
    $this->resolution = $resolution;
    $this->tracks = array();
  }

  /**
   * @param \PhpTabs\Reader\Midi\MidiTrack $track
   */
  public function addTrack(MidiTrack $track)
  {
    $this->tracks[] = $track;
  }

  /**
   * @param integer $index
   *
   * @return \PhpTabs\Reader\Midi\MidiTrack
   */
  public function getTrack($index)
  {
    return isset($this->tracks[$index])
        ? $this->tracks[$index] : null;
  }

  /**
   * Counts MIDI tracks
   *
   * @return integer
   */
  public function countTracks()
  {
    return count($this->tracks);
  }

  /**
   * @return float
   */
  public function getDivisionType()
  {
    return $this->divisionType;
  }

  /**
   * @return integer
   */
  public function getResolution()
  {
    return $this->resolution;
  }

  public function finish()
  {
    for ($i = 0; $i < count($this->tracks); $i++)
    {
      $track = $this->tracks[$i];

      $track->add(new MidiEvent(MidiMessage::metaMessage(47, 1), $track->ticks()));
    }
  }
}
