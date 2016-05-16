<?php

namespace PhpTabs\Reader\Midi;

/**
 * Midi Sequence
 */
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
  private $tracks = array();

  /**
   * MidiSequence contructor
   * 
   * @return void
   */
  public function __construct($divisionType, $resolution)
  {
    $this->divisionType = $divisionType;
    $this->resolution = $resolution;
    $this->tracks = array();
  }

  /**
   * @param MidiTrack $track
   * @return void
   */
  public function addTrack(MidiTrack $track)
  {
    $this->tracks[] = $track;
  }

  /**
   * @param integer $index
   * @return MidiTrack
   */
  public function getTrack($index)
  {
    return isset($this->tracks[$index])
      ? $this->tracks[$index] : null;
  }

  /**
   * Count MIDI tracks
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

  /**
   * @return void
   */
  public function finish()
  {
    for($i = 0; $i < count($this->tracks); $i++)
    {
      $track = $this->tracks[$i];
      $track->add(new MidiEvent(MidiMessage::metaMessage(47, 1), $track->ticks()));
    }
  }
}
