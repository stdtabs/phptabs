<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro\Writers;

use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;
use PhpTabs\Music\TimeSignature;

class MeasureHeaderWriter
{
  private $writer;

  public function __construct($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  public function writeMeasureHeaders(Song $song)
  {
    $timeSignature = new TimeSignature();

    if ($song->countMeasureHeaders()) {
      foreach ($song->getMeasureHeaders() as $header) {
        $this->writeMeasureHeader($header, $timeSignature);
        $timeSignature->setNumerator($header->getTimeSignature()->getNumerator());
        $timeSignature->getDenominator()->setValue(
          $header->getTimeSignature()->getDenominator()->getValue()
        );
      }
    }
  }

  /**
   * @param \PhpTabs\Music\MeasureHeader $measure
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   */
  private function writeMeasureHeader(MeasureHeader $measure, TimeSignature $timeSignature)
  {
    $flags = 0;

    if ($measure->getNumber() == 1 || $measure->getTimeSignature()->getNumerator() != $timeSignature->getNumerator()) {
      $flags |= 0x01;
    }

    if ($measure->getNumber() == 1 || $measure->getTimeSignature()->getDenominator()->getValue() != $timeSignature->getDenominator()->getValue()) {
      $flags |= 0x02;
    }

    if ($measure->isRepeatOpen()) {
      $flags |= 0x04;
    }

    if ($measure->getRepeatClose() > 0) {
      $flags |= 0x08;
    }

    if ($measure->hasMarker()) {
      $flags |= 0x20;
    }

    $this->writer->writeUnsignedByte($flags);

    if (($flags & 0x01) != 0) {
      $this->writer->writeByte($measure->getTimeSignature()->getNumerator());
    }

    if (($flags & 0x02) != 0) {
      $this->writer->writeByte($measure->getTimeSignature()->getDenominator()->getValue());
    }

    if (($flags & 0x08) != 0) {
      $this->writer->writeByte($measure->getRepeatClose());
    }

    if (($flags & 0x20) != 0) {
      $this->writer->writeMarker($measure->getMarker());
    }
  }
}
