<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Lyric;
use PhpTabs\Model\Song;
use PhpTabs\Model\Track;
use PhpTabs\Model\TabString;

class GuitarPro4Track extends AbstractReader
{
  /**
   * Reads track informations
   *
   * @param \PhpTabs\Model\Song $song
   * @param integer $number
   * @param array $channels an array of Channel objects
   * @param \PhpTabs\Model\Lyric $lyrics
   *
   * @return \PhpTabs\Model\Track
   */
  public function readTrack(Song $song, $number, array $channels = [], Lyric $lyrics)
  {
    $track = new Track();

    $track->setSong($song);
    $track->setNumber($number);
    $track->setLyrics($lyrics);

    $this->reader->readUnsignedByte();

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

    return $track;
  }
}
