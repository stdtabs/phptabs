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
    $flags1 = $this->createFlags1($noteEffect, $beat);
    $flags2 = $this->createFlags2($noteEffect);

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
      $this->writeStroke(
        $beat,
        ($this->writer->getName() == 'GuitarPro5Writer' ? Stroke::STROKE_UP : Stroke::STROKE_DOWN),
        ($this->writer->getName() == 'GuitarPro5Writer' ? Stroke::STROKE_DOWN : Stroke::STROKE_UP)
      );
    }
  }

  /**
   * Create flag1
   * 
   * @param  \PhpTabs\Music\NoteEffect 
   * @return int
   */
  public function createFlags1(NoteEffect $effect, Beat $beat)
  {
    $flags1 = 0;

    if ($effect->isFadeIn()) {
      $flags1 |= 0x10;
    }

    if ($effect->isTapping() || $effect->isSlapping() || $effect->isPopping()) {
      $flags1 |= 0x20;
    }

    if ($beat->getStroke()->getDirection() !== Stroke::STROKE_NONE) {
      $flags1 |= 0x40;
    }

    return $flags1;
  }

  /**
   * Create flag2
   * 
   * @param  \PhpTabs\Music\NoteEffect $effect
   * @return int
   */
  public function createFlags2(NoteEffect $effect)
  {
    $flags2 = 0;

    if ($effect->isTremoloBar()) {
      $flags2 |= 0x04;
    }

    return $flags2;
  }

  /**
   * Write stroke values
   * 
   * @param  \PhpTabs\Music\Beat $beat
   * @param  int $firstTest
   * @param  int $secondTest
   */
  public function writeStroke(Beat $beat, $firstTest, $secondTest)
  {
    $this->writer->writeUnsignedByte(
      $beat->getStroke()->getDirection() == $firstTest
        ? $this->writer->toStrokeValue($beat->getStroke()) : 0
    );

    $this->writer->writeUnsignedByte(
      $beat->getStroke()->getDirection() == $secondTest
        ? $this->writer->toStrokeValue($beat->getStroke()) : 0
    );
  }
}
