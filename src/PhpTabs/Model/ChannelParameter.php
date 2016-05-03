<?php

namespace PhpTabs\Model;

/**
 * ChannelParameter
 */

class ChannelParameter
{
  private $key;
  private $value;

  public function getKey()
  {
    return $this->key;
  }

  public function setKey($key)
  {
    $this->key = $key;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function copyFrom(ChannelParameter $channelParameter)
  {
    $this->setKey($channelParameter->getKey());
    $this->setValue($channelParameter->getValue());
  }

  public function __clone()
  {
    $channelParameter = new ChannelParameter();
    $channelParameter->copyFrom($this);
    return $channelParameter;
  }
}
