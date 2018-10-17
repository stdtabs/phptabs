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

  /**
   * @param int $channelId
   */
  public function __construct($channelId)
  {
    $this->channelId = $channelId;
  }

  /**
   * @return int
   */
  public function getChannelId()
  {
    return $this->channelId;
  }

  /**
   * @return int
   */
  public function getChannel1()
  {
    return $this->channel1;
  }

  /**
   * @param int $channel1
   */
  public function setChannel1($channel1)
  {
    $this->channel1 = $channel1;
  }

  /**
   * @return int
   */
  public function getChannel2()
  {
    return $this->channel2;
  }

  /**
   * @param int $channel2
   */
  public function setChannel2($channel2)
  {
    $this->channel2 = $channel2;
  }

  /**
   * Compare current channel route with a given channel route
   * 
   * @param \PhpTabs\Share\ChannelRoute $route
   * @return bool
   */
  public function equals(ChannelRoute $route)
  {
    return $this->channelId == $route->getChannelId()
        && $this->channel1  == $route->getChannel1()
        && $this->channel2  == $route->getChannel2();
  }
}
