<?php

namespace PhpTabs\Writer\Midi;

use PhpTabs\Model\ChannelRoute;
use PhpTabs\Model\ChannelRouter;
use PhpTabs\Model\Duration;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Reader\Midi\MidiEvent;
use PhpTabs\Reader\Midi\MidiSequence;
use PhpTabs\Reader\Midi\MidiTrack;

class MidiSequenceHandler
{
  private $sequence;
  private $router;
  private $tracks;
  private $writer;

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

    for ($i = 0; $i < $this->getTracks(); $i++)
    {
      $this->sequence->addTrack(new MidiTrack());
    }
  }

  public function getSequence()
  {
    return $this->sequence;
  }

  public function getTracks()
  {
    return $this->tracks;
  }

  private function resolveChannel(ChannelRoute $channel, $bendMode)
  {
    return $bendMode ? $channel->getChannel2() : $channel->getChannel1();
  }

  public function addEvent($track, MidiEvent $event)
  {
    if($track >= 0 && $track < $this->getSequence()->countTracks())
    {
      $this->getSequence()->getTrack($track)->add($event);
    }
  }

  public function addNoteOff($tick, $track, $channelId, $note, $velocity, $bendMode)
  {
    $channel = $this->router->getRoute($channelId);

    if( $channel !== null )
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::noteOff($this->resolveChannel($channel, $bendMode), $note, $velocity), $tick));
    }
  }

  public function addNoteOn($tick, $track, $channelId, $note, $velocity, $bendMode)
  {
    $channel = $this->router->getRoute($channelId);

    if( $channel !== null )
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::noteOn($this->resolveChannel($channel, $bendMode), $note, $velocity), $tick));
    }
  }

  public function addPitchBend($tick, $track, $channelId, $value, $bendMode)
  {
    $channel = $this->router->getRoute($channelId);

    if( $channel !== null )
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::pitchBend($this->resolveChannel($channel, $bendMode), $value), $tick));
    }
  }

  public function addControlChange($tick, $track, $channelId, $controller, $value)
  {
    $channel = $this->router->getRoute($channelId);

    if( $channel !== null )
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::controlChange($channel->getChannel1(), $controller, $value), $tick));

      if( $channel->getChannel1() != $channel->getChannel2() )
      {
        $this->addEvent($track, new MidiEvent(MidiMessageUtils::controlChange($channel->getChannel2(), $controller, $value), $tick));
      }
    }
  }

  public function addProgramChange($tick, $track, $channelId, $instrument)
  {
    $channel = $this->router->getRoute($channelId);

    if( $channel !== null )
    {
      $this->addEvent($track, new MidiEvent(MidiMessageUtils::programChange($channel->getChannel1(), $instrument), $tick));

      if( $channel->getChannel1() != $channel->getChannel2() )
      {
        $this->addEvent($track, new MidiEvent(MidiMessageUtils::programChange($channel->getChannel2(), $instrument), $tick));
      }
    }
  }

  public function addTempoInUSQ($tick, $track, $usq)
  {
    $this->addEvent($track, new MidiEvent(MidiMessageUtils::tempoInUSQ($usq), $tick));
  }

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
