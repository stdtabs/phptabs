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

final class ChannelRoute
{
    public const PARAMETER_CHANNEL_1 = 'channel-1';
    public const PARAMETER_CHANNEL_2 = 'channel-2';
    public const NULL_VALUE = -1;

    /**
     * @var int
     */
    private $channelId;

    /**
     * @var int
     */
    private $channel1;

    /**
     * @var int
     */
    private $channel2;

    public function __construct(int $channelId)
    {
        $this->channelId = $channelId;
        $this->channel1 = self::NULL_VALUE;
        $this->channel2 = self::NULL_VALUE;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getChannel1(): int
    {
        return $this->channel1;
    }

    public function setChannel1(int $channel1): void
    {
        $this->channel1 = $channel1;
    }

    public function getChannel2(): int
    {
        return $this->channel2;
    }

    public function setChannel2(int $channel2): void
    {
        $this->channel2 = $channel2;
    }

    /**
     * Compare current channel route with a given channel route
     */
    public function equals(ChannelRoute $route): bool
    {
        return $this->channelId === $route->getChannelId()
            && $this->channel1  === $route->getChannel1()
            && $this->channel2  === $route->getChannel2();
    }
}
