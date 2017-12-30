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

use PhpTabs\Music\Beat;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Stroke;
use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface as GprInterface;

class BeatEffectWriter
{
  private $writer;

  public function __construct($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @param \PhpTabs\Music\Beat       $beat
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  public function writeBeatEffects(Beat $beat, NoteEffect $noteEffect)
  {
    $flags1 = 0;
    $flags2 = 0;

    if ($noteEffect->isFadeIn()) {
      $flags1 |= 0x10;
    }

    if ($noteEffect->isTapping() || $noteEffect->isSlapping() || $noteEffect->isPopping()) {
      $flags1 |= 0x20;
    }

    if ($noteEffect->isTremoloBar()) {
      $flags2 |= 0x04;
    }

    if ($beat->getStroke()->getDirection() !== Stroke::STROKE_NONE) {
      $flags1 |= 0x40;
    }

    $this->writer->writeUnsignedByte($flags1);
    $this->writer->writeUnsignedByte($flags2);

    if (($flags1 & 0x20) != 0) {
      if ($noteEffect->isTapping()) {
        $this->writer->writeUnsignedByte(1);
      } elseif ($noteEffect->isSlapping()) {
        $this->writer->writeUnsignedByte(2);
      } elseif ($noteEffect->isPopping()) {
        $this->writer->writeUnsignedByte(3);
      }
    }

    if (($flags2 & 0x04) != 0) {
      $this->writer->getWriter('NoteEffectWriter')
           ->writeTremoloBar($noteEffect->getTremoloBar());
    }

    if (($flags1 & 0x40) != 0) {
      $this->writer->writeUnsignedByte(
        $beat->getStroke()->getDirection() === Stroke::STROKE_DOWN
          ? $this->writer->toStrokeValue($beat->getStroke()) : 0
      );

      $this->writer->writeUnsignedByte(
        $beat->getStroke()->getDirection() === Stroke::STROKE_UP
          ? $this->writer->toStrokeValue($beat->getStroke()) : 0
      );
    }
  }
}
