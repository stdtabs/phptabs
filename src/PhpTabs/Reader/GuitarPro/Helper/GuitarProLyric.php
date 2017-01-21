<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Lyric;

class GuitarProLyric extends AbstractReader
{
  /**
   * Reads lyrics informations
   * 
   * @return \PhpTabs\Model\Lyric
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
