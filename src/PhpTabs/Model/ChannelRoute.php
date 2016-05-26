<?php

namespace PhpTabs\Model;

class ChannelRoute
{
  const PARAMETER_CHANNEL_1 = "channel-1";
  const PARAMETER_CHANNEL_2 = "channel-2";

  const NULL_VALUE = -1;

  private $channelId;
  private $channel1;
  private $channel2;

  public function __construct($channelId)
  {
    $this->channelId = $channelId;
  }

  public function getChannelId()
  {
    return $this->channelId;
  }

  public function getChannel1()
  {
    return $this->channel1;
  }

  public function setChannel1($channel1)
  {
    $this->channel1 = $channel1;
  }

  public function getChannel2()
  {
    return $this->channel2;
  }

  public function setChannel2($channel2)
  {
    $this->channel2 = $channel2;
  }
}
