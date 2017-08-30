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

use PhpTabs\Music\Song;

class GuitarPro3RepeatAlternative extends AbstractReader
{
  /**
   * Parses repeat alternative
   * 
   * @param \PhpTabs\Music\Song $song
   * @param integer $measure
   * 
   * @return integer Number of repeat alternatives
   */
  public function parseRepeatAlternative(Song $song, $measure)
  {
    $value = $this->reader->readUnsignedByte();

    $repeatAlternative = 0;
    $existentAlternatives = 0;
    $headers = $song->getMeasureHeaders();

    foreach ($headers as $header)
    {
      if ($header->getNumber() == $measure)
      {
        break;
      }

      if ($header->isRepeatOpen())
      {
        $existentAlternatives = 0;
      }

      $existentAlternatives |= $header->getRepeatAlternative();
    }

    for ($i = 0; $i < 8; $i++)
    {
      if ($value > $i && ($existentAlternatives & (1 << $i)) == 0)
      {
        $repeatAlternative |= (1 << $i);
      }
    }

    return $repeatAlternative;
  }
}
