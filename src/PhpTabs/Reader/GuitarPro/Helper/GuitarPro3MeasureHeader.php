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

use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;
use PhpTabs\Music\TimeSignature;

class GuitarPro3MeasureHeader extends AbstractReader
{
  /**
   * Reads a mesure header
   * 
   * @param integer $number
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   * @param integer $tempoValue
   * 
   * @return \PhpTabs\Music\MeasureHeader
   */
  public function readMeasureHeader($number, Song $song, TimeSignature $timeSignature, $tempoValue = 120)
  {
    $flags = $this->reader->readUnsignedByte();
    $header = new MeasureHeader();
    $header->setNumber($number);
    $header->setStart(0);
    $header->getTempo()->setValue($tempoValue);
    $header->setTripletFeel($this->reader->getTripletFeel());
    $header->setRepeatOpen( (($flags & 0x04) != 0) );

    if (($flags & 0x01) != 0)
    {
      $timeSignature->setNumerator($this->reader->readByte());
    }

    if (($flags & 0x02) != 0)
    {
      $timeSignature->getDenominator()->setValue($this->reader->readByte());
    }

    $header->getTimeSignature()->copyFrom($timeSignature);

    if (($flags & 0x08) != 0)
    {
      $header->setRepeatClose($this->reader->readByte());
    }

    if (($flags & 0x10) != 0)
    {
      $header->setRepeatAlternative($this->reader->factory('GuitarPro3RepeatAlternative')->parseRepeatAlternative($song, $number));
    }

    if (($flags & 0x20) != 0)
    {
      $header->setMarker($this->reader->factory('GuitarProMarker')->readMarker($number));
    }

    if (($flags & 0x40) != 0)
    {
      $this->reader->setKeySignature($this->reader->factory('GuitarProKeySignature')->readKeySignature());
      $this->reader->skip(1);
    }

    return $header;
  }
}
