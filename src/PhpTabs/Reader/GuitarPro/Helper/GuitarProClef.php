<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Measure;
use PhpTabs\Music\Track;
use PhpTabs\Music\Song;

class GuitarProClef extends AbstractReader
{
  /**
   * @param \PhpTabs\Music\Track $track
   *
   * @return integer Clef of $track
   */
  public function getClef(Track $track)
  {
    if (!$this->isPercussionChannel($track->getSong(), $track->getChannelId()))
    {
      $strings = $track->getStrings();

      foreach ($strings as $string)
      {
        if ($string->getValue() <= 34)
        {
          return Measure::CLEF_BASS;
        }
      }
    }

    return Measure::CLEF_TREBLE;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * @param integer $channelId
   *
   * @return boolean
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
