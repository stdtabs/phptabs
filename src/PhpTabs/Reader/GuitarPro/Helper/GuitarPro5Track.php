<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Lyric;
use PhpTabs\Model\Song;
use PhpTabs\Model\Track;
use PhpTabs\Model\TabString;

class GuitarPro5Track extends AbstractReader
{
  /**
   * Reads Track informations
   *
   * @param Song $song
   * @param integer $number
   * @param array $channels an array of Channel objects
   * @param Lyric $lyrics
   *
   * @return Track
   */
  public function readTrack(Song $song, $number, $channels, Lyric $lyrics)
  {
    $this->reader->readUnsignedByte();
    if($number == 1 || $this->reader->getVersionIndex() == 0)
    {
      $this->reader->skip();
    }

    $track = new Track();
    $track->setSong($song);
    $track->setNumber($number);
    $track->setLyrics($lyrics);
    $track->setName($this->reader->readStringByte(40));

    $stringCount = $this->reader->readInt();

    for ($i = 0; $i < 7; $i++)
    {
      $tuning = $this->reader->readInt();
      if ($stringCount > $i)
      {
        $string = new TabString();
        $string->setNumber($i + 1);
        $string->setValue($tuning);
        $track->addString($string);
      }
    }

    $this->reader->readInt();
    $this->reader->factory('GuitarProChannel')->readChannel($song, $track, $channels);
    $this->reader->readInt();
    $track->setOffset($this->reader->readInt());
    $this->reader->factory('GuitarProColor')->readColor($track->getColor());

    $this->reader->skip($this->reader->getVersionIndex() > 0 ? 49 : 44);

    if($this->reader->getVersionIndex() > 0)
    {
      $this->reader->readStringByteSizeOfInteger();
      $this->reader->readStringByteSizeOfInteger();
    }

    return $track;
  }
}
