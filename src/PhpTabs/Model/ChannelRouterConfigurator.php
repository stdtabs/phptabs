<?php

namespace PhpTabs\Model;

class ChannelRouterConfigurator
{
  private $router;

  public function __construct(ChannelRouter $router)
  {
    $this->router = $router;
  }

  public function configureRouter(array $channels)
  {
    $this->router->resetRoutes();

    foreach($channels as $channel)
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

  private function getIntegerChannelParameter(Channel $channel, $key)
  {
    $channelParameter = $this->findChannelParameter($channel, $key);
    if($channelParameter !== null && $channelParameter->getValue() !== null)
    {
      return intval($channelParameter->getValue());
    }

    return ChannelRoute::NULL_VALUE;
  }

  private function findChannelParameter(Channel $channel, $key)
  {
    $parameters = $channel->getParameters();

    foreach($parameters as $parameter)
    {
      if($parameter->getKey() == $key)
      {
        return $parameter;
      }
    }

    return null;
  }
}
