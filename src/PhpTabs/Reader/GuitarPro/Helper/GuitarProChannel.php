<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Channel;
use PhpTabs\Music\ChannelNames;
use PhpTabs\Music\ChannelParameter;
use PhpTabs\Music\Song;
use PhpTabs\Music\Track;

class GuitarProChannel extends AbstractReader
{
  /**
   * Reads Channel informations
   * 
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\Track $track
   * @param array $channels
   */
  public function readChannel(Song $song, Track $track, array $channels)
  {
    $gChannel1 = $this->reader->readInt() - 1;
    $gChannel2 = $this->reader->readInt() - 1;

    if ($gChannel1 >= 0 && $gChannel1 < count($channels))
    {
      $channel = new Channel();
      $gChannel1Param = new ChannelParameter();
      $gChannel2Param = new ChannelParameter();

      $gChannel1Param->setKey("channel-1");
      $gChannel1Param->setValue("$gChannel1");
      $gChannel2Param->setKey("channel-2");
      $gChannel2Param->setValue($gChannel1 != 9
        ? "$gChannel2" : "$gChannel1");

      $channel->copyFrom($channels[$gChannel1]);

      for ($i = 0; $i < $song->countChannels(); $i++)
      {
        $channelAux = $song->getChannel($i);

        for ($n = 0; $n < $channelAux->countParameters(); $n++)
        {
          $channelParameter = $channelAux->getParameter($n);

          if ($channelParameter->getKey() == "$gChannel1")
          {
            if ("$gChannel1" == $channelParameter->getValue())
            {
              $channel->setChannelId($channelAux->getChannelId());
            }
          }
        }
      }

      if ($channel->getChannelId() <= 0)
      {
        $channel->setChannelId($song->countChannels() + 1);
        $channel->setName($this->createChannelNameFromProgram($song, $channel));
        $channel->addParameter($gChannel1Param);
        $channel->addParameter($gChannel2Param);

        $song->addChannel($channel);
      }

      $track->setChannelId($channel->getChannelId());
    }
  }

  /**
   * Creates a channel name with a program
   * 
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\Channel $channel
   *
   * @return string a new channel name
   */
  protected function createChannelNameFromProgram(Song $song, $channel)
  {
    $names = ChannelNames::$defaultNames;

    if ($channel->getProgram() >= 0 && isset($names[$channel->getProgram()]))
    {
      return $this->createChannelName($song, $names[$channel->getProgram()]);
    }

    return $this->createDefaultChannelName($song);
  }

  /**
   * Creates a channel
   * 
   * @param \PhpTabs\Music\Song $song
   *
   * @return string a generated channel name
   */
  protected function createDefaultChannelName(Song $song)
  {
    return $this->createChannelName($song, 'Unnamed');
  }

  /**
   * Generates a channel name
   * 
   * @param \PhpTabs\Music\Song $song
   * @param string $prefix
   *
   * @return string channel name
   */
  protected function createChannelName(Song $song, $prefix)
  {
    $number = 0;
    $unusedName = null;

    while ($unusedName === null)
    {
      $number ++;
      $name = $prefix . ' ' . $number;

      if (!$this->findChannelsByName($song, $name))
      {
        $unusedName = $name;
      }
    }

    return $unusedName;
  }

  /**
   * Checks if a channel is still defined
   *
   * @param \PhpTabs\Music\Song $song
   * @param string $name
   *
   * @return boolean Result of the search
   */
  protected function findChannelsByName(Song $song, $name)
  {
    $channels = $song->getChannels();

    foreach ($channels as $v)
    {
      if ($v->getName() == $name)
      {
        return true;
      }
    }

    return false;
  }
}
