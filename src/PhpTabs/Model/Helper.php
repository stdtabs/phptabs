<?php

namespace PhpTabs\Model;

/**
 * @uses ChannelNames
 * @uses Song
 * @uses TabString
 */
class Helper
{
  public static function createPercussionStrings($stringCount)
  {
    return Helper::createStrings($stringCount);
  }

  /**
   * Creates a set of strings
   * 
   * @param integer $stringCount
   * @param array $defaultTunings A multidim array of integer
   * @return array
   */
  public static function createStrings($stringCount, $defaultTunings = array())
  {
    $strings = array();

    if(count($defaultTunings))
    {
      for($i = 0; $i < count($defaultTunings); $i++)
      {
        if($stringCount == count($defaultTunings[$i]))
        {
          for($n = 0; $n < $stringCount; $n++)
          {
            $strings[] = new TabString(($n + 1), $defaultTunings[$i][$n]);
          }
          break;
        }
      }
    }

    if(!count($strings))
    {
      for($i = 1; $i <= $stringCount; $i++)
      {
        $strings[] = new TabString($i, 0);
      }
    }

    return $strings;
  }

  /**
   * Checks if a channel is still defined
   *
   * @param Song $song
   * @param string $name
   * @return boolean Result of the search
   */
  public function findChannelsByName($song, $name)
  {
    $channels = $song->getChannels();

    foreach($channels as $v)
    {
      if($v->getName() == $name)
        return true;
    }

    return false;
  }

  /**
   * Generates a channel name
   * 
   * @param Song $song
   * @param string $prefix
   * @return string channel name
   */
  public function createChannelName(Song $song, $prefix)
  {
    $number = 0;
    $unusedName = null;

    while( $unusedName == null )
    {
      $number ++;
      $name = $prefix . " " . $number;
      if(!$this->findChannelsByName($song, $name))
      {
        $unusedName = $name;
      }
    }

    return $unusedName;
  }

  /**
   * Creates a channel
   * 
   * @param Song $song
   * @return string a generated channel name
   */
  public function createDefaultChannelName(Song $song)
  {
    return $this->createChannelName($song, "Unnamed");
  }

  /**
   * Creates a channel name with a program
   * 
   * @param Song $song
   * @param Channel $channel
   * @return string a new channel name
   */
  public function createChannelNameFromProgram(Song $song, $channel)
  {
    $names = ChannelNames::$defaultNames;

    if($channel->getProgram() >= 0 && isset($names[$channel->getProgram()]))
    {
      return $this->createChannelName($song, $names[$channel->getProgram()]);
    }

    return $this->createDefaultChannelName($song);
  }

}
