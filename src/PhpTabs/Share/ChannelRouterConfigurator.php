<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Share;

use PhpTabs\Music\Channel;
use PhpTabs\Music\ChannelParameter;

final class ChannelRouterConfigurator
{
    /**
     * @var ChannelRouter
     */
    private $router;

    public function __construct(ChannelRouter $router)
    {
        $this->router = $router;
    }

    /**
     * @param array<Channel> $channels
     */
    public function configureRouter(array $channels): void
    {
        $this->router->resetRoutes();

        array_walk(
            $channels, function ($channel): void {
                $channelRoute = new ChannelRoute($channel->getId());

                $channelRoute->setChannel1(
                    $this->getIntegerChannelParameter($channel, ChannelRoute::PARAMETER_CHANNEL_1)
                );

                $channelRoute->setChannel2(
                    $this->getIntegerChannelParameter($channel, ChannelRoute::PARAMETER_CHANNEL_2)
                );

                $this->router->configureRoutes($channelRoute, $channel->isPercussionChannel());
            }
        );
    }

    private function getIntegerChannelParameter(Channel $channel, string $key): int
    {
        $channelParameter = $this->findChannelParameter($channel, $key);

        if ($channelParameter !== null && $channelParameter->getValue() !== null) {
            return intval($channelParameter->getValue());
        }

        return ChannelRoute::NULL_VALUE;
    }

    private function findChannelParameter(Channel $channel, string $key): ?ChannelParameter
    {
        $parameters = $channel->getParameters();

        foreach ($parameters as $parameter) {
            if ($parameter->getKey() === $key) {
                return $parameter;
            }
        }

        return null;
    }
}
