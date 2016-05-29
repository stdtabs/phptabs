<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Channel;

class GuitarProChannels
{
  /**
   * Reads channels informations
   * 
   * @return array $channels
   */
  public function readChannels(GuitarProReaderInterface $reader)
  {
    $channels = array();

    for ($i=0; $i<64; $i++)
    {
      $channel = new Channel();
      $channel->setProgram($reader->readInt());
      $channel->setVolume($this->toChannelShort($reader->readByte()));
      $channel->setBalance($this->toChannelShort($reader->readByte()));
      $channel->setChorus($this->toChannelShort($reader->readByte()));
      $channel->setReverb($this->toChannelShort($reader->readByte()));
      $channel->setPhaser($this->toChannelShort($reader->readByte()));
      $channel->setTremolo($this->toChannelShort($reader->readByte()));
      $channel->setBank($i == 9
        ? Channel::DEFAULT_PERCUSSION_BANK : Channel::DEFAULT_BANK);

      if ($channel->getProgram() < 0)
      {
        $channel->setProgram(0);
      }

      $channels[] = $channel;

      $reader->skip(2);
    }

    return $channels;
  }

  /**
   * Formats an integer
   * 
   * @param byte $b
   * @return integer between 0 and 32767
   */
  protected function toChannelShort($bytes)
  {
    $value = ($bytes * 8) - 1;

    return max($value, 0);
  }
}
