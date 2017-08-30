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
use PhpTabs\Music\Track;
use PhpTabs\Music\TabString;

class GuitarPro3Track extends AbstractReader
{
  /**
   * Reads track informations
   * 
   * @param \PhpTabs\Music\Song $song
   * @param integer $number
   * @param array $channels An array of Channel objects
   * 
   * @return Track
   */
  public function readTrack(Song $song, $number, array $channels = [])
  {
    $track = new Track();
    $track->setSong($song);
    $track->setNumber($number);
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
