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

use PhpTabs\Music\Lyric;

class GuitarProLyric extends AbstractReader
{
  /**
   * Reads lyrics informations
   * 
   * @return \PhpTabs\Music\Lyric
   */
  public function readLyrics()
  {
    $lyric = new Lyric();
    $lyric->setFrom($this->reader->readInt());
    $lyric->setLyrics($this->reader->readStringInteger());

    for ($i = 0; $i < 4; $i++)
    {
      $this->reader->readInt();
      $this->reader->readStringInteger();
    }

    return $lyric;
  }
}
