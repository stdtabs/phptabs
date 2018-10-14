<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Midi;

use PhpTabs\Share\ChannelRoute;
use PhpTabs\Share\ChannelRouter;
use PhpTabs\Music\Duration;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Reader\Midi\MidiEvent;
use PhpTabs\Reader\Midi\MidiSequence;
use PhpTabs\Reader\Midi\MidiTrack;

class MidiSequenceHandler
{
  private $sequence;
  private $router;
  private $tracks;
  private $writer;

  /**
   * @param int $tracks
   * @param \PhpTabs\Music\ChannelRouter $router
   * @param \PhpTabs\Model\MidiWriter $writer
   */
  public function __construct($tracks, ChannelRouter $router, MidiWriter $writer)
  {
    $this->router = $router;
    $this->tracks = $tracks;
    $this->writer = $writer;
    $this->init();
  }

  private function init()
  {
    $this->sequence = new MidiSequence(MidiSequence::PPQ, Duration::QUARTER_TIME);

    for ($i = 0; $i < $this->getTracks(); $i++) {
      $this->sequence->addTrack(new MidiTrack());
    }
  }

  /**
   * @return string
   */
  public function getSequence()
  {
    return $this->sequence;
  }

  /**
   * @return int
   */
  public function getTracks()
  {
    return $this->tracks;
  }

  /**
   * @param \PhpTabs\Music\ChannelRoute $channel
   * @param bool $bendMode
   * 
   * @return \PhpTabs\Music\Channel
   */
  private function resolveChannel(ChannelRoute $channel, $bendMode)
  {
    return $bendMode ? $channel->getChannel2() : $channel->getChannel1();
  }

  /**
   * @param int $track
   * @param \PhpTabs\Reader\Midi\MidiEvent $event
   */
  public function addEvent($track, MidiEvent $event)
  {
    if ($track >= 0 && $track < $this->getSequence()->countTracks())
    {
      $this->getSequence()->getTrack($track)->add($event);
    }
  }

  /**
   * @param int $tick
   * @param int $track
   * @param int $channelId
   * @param int $note
   * @param int $velocity
   * @param bool $bendMode
   */
  public function addNoteOff($tick, $track, $channelId, $note, $velocity, $bendMode)
  {
    $channel = $this->router->getRoute($channelId);

    if ($channel !== null) {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::noteOff($this->resolveChannel($channel, $bendMode), $note, $velocity), $tick));
    }
  }

  /**
   * @param int $tick
   * @param int $track
   * @param int $channelId
   * @param int $note
   * @param int $velocity
   * @param bool $bendMode
   */
  public function addNoteOn($tick, $track, $channelId, $note, $velocity, $bendMode)
  {
    $channel = $this->router->getRoute($channelId);

    if ($channel !== null) {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::noteOn($this->resolveChannel($channel, $bendMode), $note, $velocity), $tick));
    }
  }

  /**
   * @param int $tick
   * @param int $track
   * @param int $channelId
   * @param int $value
   * @param bool $bendMode
   */
  public function addPitchBend($tick, $track, $channelId, $value, $bendMode)
  {
    $channel = $this->router->getRoute($channelId);

    if ($channel !== null) {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::pitchBend($this->resolveChannel($channel, $bendMode), $value), $tick));
    }
  }

  /**
   * @param int $tick
   * @param int $track
   * @param int $channelId
   * @param int $controller
   * @param int $value
   */
  public function addControlChange($tick, $track, $channelId, $controller, $value)
  {
    $channel = $this->router->getRoute($channelId);

    if ($channel !== null)
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::controlChange($channel->getChannel1(), $controller, $value), $tick));

      if ($channel->getChannel1() != $channel->getChannel2())
      {
        $this->addEvent($track, new MidiEvent(MidiMessageUtils::controlChange($channel->getChannel2(), $controller, $value), $tick));
      }
    }
  }

  /**
   * @param int $tick
   * @param int $track
   * @param int $channelId
   * @param int $instrument
   */
  public function addProgramChange($tick, $track, $channelId, $instrument)
  {
    $channel = $this->router->getRoute($channelId);

    if ($channel !== null)
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::programChange($channel->getChannel1(), $instrument), $tick));

      if ($channel->getChannel1() != $channel->getChannel2())
      {
        $this->addEvent($track, new MidiEvent(MidiMessageUtils::programChange($channel->getChannel2(), $instrument), $tick));
      }
    }
  }

  /**
   * @param int $tick
   * @param int $track
   * @param int $usq
   */
  public function addTempoInUSQ($tick, $track, $usq)
  {
    $this->addEvent($track, new MidiEvent(MidiMessageUtils::tempoInUSQ($usq), $tick));
  }

  /**
   * @param int $tick
   * @param int $track
   * @param \Phptabs\Model\TimeSignature $timeSignature)
   */
  public function addTimeSignature($tick, $track, TimeSignature $timeSignature)
  {
    $this->addEvent($track, new MidiEvent(MidiMessageUtils::timeSignature($timeSignature), $tick));
  }

  public function notifyFinish()
  {
    $this->getSequence()->finish();
    $this->writer->write($this->getSequence(), 1);
  }
}
