<?php

namespace PhpTabs\Model;

/**
 * @uses ChannelNames
 * @uses Song
 */
class Helper
{
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
    $names = ChannelNames::$DEFAULT_NAMES;

    if($channel->getProgram() >= 0 && isset($names[$channel->getProgram()]))
    {
      return $this->createChannelName($song, ChannelNames::$DEFAULT_NAMES[$channel->getProgram()]);
    }

    return $this->createDefaultChannelName($song);
  }

}
