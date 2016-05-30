<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Measure;
use PhpTabs\Model\Track;
use PhpTabs\Model\Song;

class GuitarProClef extends AbstractReader
{
  /**
   * @param Track $track
   * @return integer Clef of $track
   */
  public function getClef(Track $track)
  {
    if(!$this->isPercussionChannel($track->getSong(), $track->getChannelId()))
    {
      $strings = $track->getStrings();

      foreach($strings as $string)
      {
        if($string->getValue() <= 34)
        {
          return Measure::CLEF_BASS;
        }
      }
    }

    return Measure::CLEF_TREBLE;
  }

  protected function isPercussionChannel(Song $song, $channelId)
  {
    $channels = $song->getChannels();

    foreach($channels as $channel)
    {
      if($channel->getChannelId() == $channelId)
      {
        return $channel->isPercussionChannel();
      }
    }

    return false;
  }
}
