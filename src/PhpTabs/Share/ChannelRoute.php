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

class ChannelRoute
{
    const PARAMETER_CHANNEL_1 = "channel-1";
    const PARAMETER_CHANNEL_2 = "channel-2";

    const NULL_VALUE = -1;

    private $channelId;
    private $channel1;
    private $channel2;

    public function __construct(int $channelId)
    {
        $this->channelId = $channelId;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getChannel1(): ?string
    {
        return $this->channel1;
    }

    public function setChannel1(string $channel1)
    {
        $this->channel1 = $channel1;
    }

    public function getChannel2(): ?string
    {
        return $this->channel2;
    }

    public function setChannel2(string $channel2)
    {
        $this->channel2 = $channel2;
    }

    /**
     * Compare current channel route with a given channel route
     */
    public function equals(ChannelRoute $route): bool
    {
        return $this->channelId == $route->getChannelId()
            && $this->channel1  == $route->getChannel1()
            && $this->channel2  == $route->getChannel2();
    }
}
