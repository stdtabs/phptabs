<?php

namespace PhpTabs\Reader\Midi;

/**
 * Midi track
 */
class MidiTrack
{
  private $ticks;
  private $events = array();

  public function add(MidiEvent $event)
  {
    $this->events[] = $event;
    $this->ticks = max($this->ticks, $event->getTick());
  }

  public function get($index)
  {
    return $this->events[$index];
  }

  public function countEvents()
  {
    return count($this->events);
  }

  public function ticks()
  {
    return $this->ticks;
  }
}
