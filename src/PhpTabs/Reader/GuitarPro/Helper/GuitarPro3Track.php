<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Song;
use PhpTabs\Model\Track;
use PhpTabs\Model\TabString;

class GuitarPro3Track extends AbstractReader
{
  /**
   * Reads Track informations
   * 
   * @param Song $song
   * @param integer $number
   * @param array $channels An array of Channel objects
   * 
   * @return Track
   */
  public function readTrack(Song $song, $number, $channels)
  {
    $track = new Track();
    $track->setSong($song);
    $track->setNumber($number);
    $this->reader->readUnsignedByte();
    $track->setName($this->reader->readStringByte(40));
    $stringCount = $this->reader->readInt();

    for($i = 0; $i < 7; $i++)
    {
      $tuning = $this->reader->readInt();
      if($stringCount > $i)
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
