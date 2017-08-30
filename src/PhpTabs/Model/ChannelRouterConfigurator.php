<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Model;

use PhpTabs\Music\Channel;

class ChannelRouterConfigurator
{
  private $router;

  /**
   * @param \PhpTabs\Music\ChannelRouter $router
   */
  public function __construct(ChannelRouter $router)
  {
    $this->router = $router;
  }

  /**
   * @param array $channels
   */
  public function configureRouter(array $channels)
  {
    $this->router->resetRoutes();

    foreach ($channels as $channel)
    {
      $channelRoute = new ChannelRoute($channel->getChannelId());

      $channelRoute->setChannel1(
        $this->getIntegerChannelParameter($channel, ChannelRoute::PARAMETER_CHANNEL_1)
      );

      $channelRoute->setChannel2(
        $this->getIntegerChannelParameter($channel, ChannelRoute::PARAMETER_CHANNEL_2)
      );

      $this->router->configureRoutes($channelRoute, $channel->isPercussionChannel());
    }
  }

  /**
   * @param \PhpTabs\Music\Channel $channel
   * @param int $key
   * 
   * @return int
   */
  private function getIntegerChannelParameter(Channel $channel, $key)
  {
    $channelParameter = $this->findChannelParameter($channel, $key);

    if ($channelParameter !== null && $channelParameter->getValue() !== null)
    {
      return intval($channelParameter->getValue());
    }

    return ChannelRoute::NULL_VALUE;
  }

  /**
   * @param \PhpTabs\Music\Channel $channel
   * @param int $key
   *
   * @return \PhpTabs\Music\ChannelParameter
   */
  private function findChannelParameter(Channel $channel, $key)
  {
    $parameters = $channel->getParameters();

    foreach ($parameters as $parameter)
    {
      if ($parameter->getKey() == $key)
      {
        return $parameter;
      }
    }

    return null;
  }
}
