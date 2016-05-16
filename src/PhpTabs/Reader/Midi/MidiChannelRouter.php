<?php

namespace PhpTabs\Reader\Midi;

/**
 * Midi channel router
 */
class MidiChannelRouter
{
  const MAX_CHANNELS = 16;
  const PERCUSSION_CHANNEL = 9;

  private $midiChannels;

  public function __construct()
  {
    $this->midiChannels = array();
  }

  public function resetRoutes()
  {
    for($i = 0; $i < count($this->midiChannels); $i++)
    {
      $this->midiChannels[$i]->clear();
    }
  }

  public function removeRoute(MidiChannelRoute $route)
  {
    foreach($this->midiChannels as $k => $channel)
    {
      if($channel->getRoute() == $route)
      {
        array_splice($this->midiChannels, $k, 1);
      }
    }
  }

  public function getRoute($channelId)
  {
    foreach($this->midiChannels as $channel)
    {
      if($channel->getChannelId() == $channelId)
      {
        return $channel;
      }
    }

    return null;
  }

  public function configureRoutes(MidiChannelRoute $route, $percussionChannel)
  {
    $conflictingRoutes = null;

    foreach($this->midiChannels as $k => $channel)
    {
      if($this->getRoute($channel->getChannelId()) == $route)
      {
        array_splice($this->midiChannels, $k, 1);
      }
    }

    // Always channel 9 for percussions
    if($percussionChannel)
    {
      $route->setChannel1(MidiChannelRouter::PERCUSSION_CHANNEL);
      $route->setChannel2(MidiChannelRouter::PERCUSSION_CHANNEL);
    }
    else
    {
      // Use custom routes 
      if($route->getChannel1() >= 0)
      {
        if($route->getChannel2() < 0)
        {
          $route->setChannel2($route->getChannel1());
        }
        $conflictingRoutes = $this->findConflictingRoutes($route);
      }

      // Add default routes
      else
      {
        $freeChannels = $this->getFreeChannels();
        $route->setChannel1(count($freeChannels) > 0 ? intval($freeChannels[0]) : MidiChannelRoute::NULL_VALUE);
        $route->setChannel2(count($freeChannels) > 1 ? intval($freeChannels[1]) : $route->getChannel1());
      }
    }

    $this->midiChannels[] = $route;

    // Reconfigure conflicting routes
    if($conflictingRoutes != null)
    {
      foreach($conflictingRoutes as $conflictingRoute)
      {
        $conflictingRoute->setChannel1(MidiChannelRoute::NULL_VALUE);
        $conflictingRoute->setChannel2(MidiChannelRoute::NULL_VALUE);
        $this->configureRoutes($conflictingRoute, false);
      }
    }
  }

  public function findConflictingRoutes(MidiChannelRoute $channelRoute)
  {
    $routes = array();

    foreach($this->midiChannels as $route)
    {
      if($route != $channelRoute)
      {
        if($route->getChannel1() == $channelRoute->getChannel1()
          || $route->getChannel1() == $channelRoute->getChannel2()
          || $route->getChannel2() == $channelRoute->getChannel1()
          || $route->getChannel2() == $channelRoute->getChannel2())
        {
          $routes[] = $route;
        }
      }
    }

    return $routes;
  }

  public function getFreeChannels($forRoute = null)
  {
    $freeChannels = array();

    for($ch = 0; $ch < MidiChannelRouter::MAX_CHANNELS; $ch++)
    {
      if($ch != MidiChannelRouter::PERCUSSION_CHANNEL)
      {
        $isFreeChannel = true;

        foreach($this->midiChannels as $route)
        {
          if($forRoute === null || !$forRoute->equals($route))
          {
            if($route->getChannel1() == $ch || $route->getChannel2() == $ch)
            {
              $isFreeChannel = false;
            }
          }
        }

        if($isFreeChannel)
        {
          $freeChannels[] = $ch;
        }
      }
    }

    return $freeChannels;
  }
}
