<?php

namespace PhpTabs\Model;

use PhpTabs\Music\Song;

/**
 * @uses ChannelNames
 * @uses Song
 * @uses TabString
 */
class Helper
{
  /**
   * @param int $stringCount
   *
   * @return array
   */
  public static function createPercussionStrings($stringCount)
  {
    return Helper::createStrings($stringCount);
  }

  /**
   * Creates a set of strings
   * 
   * @param int $stringCount
   * @param array $defaultTunings A multidim array of integer
   *
   * @return array
   */
  public static function createStrings($stringCount, $defaultTunings = array())
  {
    $strings = array();

    if (count($defaultTunings))
    {
      for ($i = 0; $i < count($defaultTunings); $i++)
      {
        if ($stringCount == count($defaultTunings[$i]))
        {
          for ($n = 0; $n < $stringCount; $n++)
          {
            $strings[] = new TabString(($n + 1), $defaultTunings[$i][$n]);
          }
          break;
        }
      }
    }

    if (!count($strings))
    {
      for ($i = 1; $i <= $stringCount; $i++)
      {
        $strings[] = new TabString($i, 0);
      }
    }

    return $strings;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * @param int $channelId
   *
   * @return bool
   */
  protected function isPercussionChannel(Song $song, $channelId)
  {
    $channels = $song->getChannels();

    foreach ($channels as $channel)
    {
      if ($channel->getChannelId() == $channelId)
      {
        return $channel->isPercussionChannel();
      }
    }

    return false;
  }
}
