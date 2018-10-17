<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Share;

class ChannelRouter
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
    for ($i = 0; $i < count($this->midiChannels); $i++) {
      $this->midiChannels[$i]->clear();
    }
  }

  /**
   * @param \PhpTabs\Share\ChannelRoute $route
   */
  public function removeRoute(ChannelRoute $route)
  {
    array_walk($this->midiChannels,
      function ($channel, $key) use ($route) {
        if ($channel->getRoute() == $route) {
          array_splice($this->midiChannels, $key, 1);
        }
      }
    );
  }

  /**
   * @param int $channelId
   * 
   * @return int
   */
  public function getRoute($channelId)
  {
    return array_reduce($this->midiChannels, $this->reducer($channelId));
  }

  private function reducer($channelId)
  {
    return function ($carry, $item) use ($channelId) {
      if ($item->getChannelId() == $channelId) {
        $carry = $item;
      }
  
      return $carry;
    };
  }

  /**
   * @param \PhpTabs\Share\ChannelRoute $route
   * @param bool $percussionChannel
   */
  public function configureRoutes(ChannelRoute $route, $percussionChannel)
  {
    $conflictingRoutes = null;

    foreach ($this->midiChannels as $key => $channel) {
      if ($this->getRoute($key) == $route) {
        array_splice($this->midiChannels, $key, 1);
      }
    }

    // Always channel 9 for percussions
    if ($percussionChannel) {
      $route->setChannel1(ChannelRouter::PERCUSSION_CHANNEL);
      $route->setChannel2(ChannelRouter::PERCUSSION_CHANNEL);
    } else {
      // Use custom routes 
      if ($route->getChannel1() >= 0) {
        if ($route->getChannel2() < 0) {
          $route->setChannel2($route->getChannel1());
        }
        $conflictingRoutes = $this->findConflictingRoutes($route);
      } else {

      // Add default routes
        $freeChannels = $this->getFreeChannels();
        $route->setChannel1(count($freeChannels) > 0 ? intval($freeChannels[0]) : ChannelRoute::NULL_VALUE);
        $route->setChannel2(count($freeChannels) > 1 ? intval($freeChannels[1]) : $route->getChannel1());
      }
    }

    $this->midiChannels[] = $route;

    // Reconfigure conflicting routes
    if ($conflictingRoutes !== null) {
      foreach ($conflictingRoutes as $conflictingRoute) {
        $conflictingRoute->setChannel1(ChannelRoute::NULL_VALUE);
        $conflictingRoute->setChannel2(ChannelRoute::NULL_VALUE);
        $this->configureRoutes($conflictingRoute, false);
      }
    }
  }

  /**
   * @param \PhpTabs\Share\ChannelRoute $channelRoute
   * 
   * @return array
   */
  public function findConflictingRoutes(ChannelRoute $channelRoute)
  {
    $routes = array();

    foreach ($this->midiChannels as $route) {
      if ($route != $channelRoute) {
        if ($route->getChannel1() == $channelRoute->getChannel1()
          || $route->getChannel1() == $channelRoute->getChannel2()
          || $route->getChannel2() == $channelRoute->getChannel1()
          || $route->getChannel2() == $channelRoute->getChannel2()
          ) {
          $routes[] = $route;
        }
      }
    }

    return $routes;
  }

  /**
   * @param \PhpTabs\Share\ChannelRoute $forRoute
   * 
   * @return array
   */
  public function getFreeChannels($forRoute = null)
  {
    $freeChannels = array();

    for ($ch = 0; $ch < ChannelRouter::MAX_CHANNELS; $ch++) {
      if ($ch != ChannelRouter::PERCUSSION_CHANNEL) {
        $isFreeChannel = true;

        foreach ($this->midiChannels as $route) {
          if ($forRoute === null || !$forRoute->equals($route)) {
            if ($route->getChannel1() == $ch || $route->getChannel2() == $ch) {
              $isFreeChannel = false;
            }
          }
        }

        if ($isFreeChannel) {
          $freeChannels[] = $ch;
        }
      }
    }

    return $freeChannels;
  }
}
