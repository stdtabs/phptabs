<?php

namespace PhpTabs\Model;

/**
 * ChannelParameter
 */
class ChannelParameter
{
  private $key;
  private $value;

  /**
   * @return int
   */
  public function getKey()
  {
    return $this->key;
  }

  /**
   * @param int $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }

  /**
   * @return int
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * @param \PhpTabs\Model\ChannelParameter $channelParameter
   */
  public function copyFrom(ChannelParameter $channelParameter)
  {
    $this->setKey($channelParameter->getKey());
    $this->setValue($channelParameter->getValue());
  }

  /**
   * @return \PhpTabs\Model\ChannelParameter
   */
  public function __clone()
  {
    $channelParameter = new ChannelParameter();
    $channelParameter->copyFrom($this);
    return $channelParameter;
  }
}
