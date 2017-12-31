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

use PhpTabs\Music\Note;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Velocities;

class Note5Writer
{
  private $writer;

  public function __construct($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @param \PhpTabs\Music\Note $note
   */
  public function writeNote(Note $note)
  {
    $effect = $note->getEffect();

    $flags = $this->createFlags($effect);

    $this->writer->writeUnsignedByte($flags);

    if (($flags & 0x20) != 0) {
      $typeHeader = 0x01;
  
      if ($note->isTiedNote()) {
        $typeHeader = 0x02;
      } elseif ($effect->isDeadNote()) {
        $typeHeader = 0x03;
      }
  
      $this->writer->writeUnsignedByte($typeHeader);
    }

    if (($flags & 0x10) != 0) {
      $this->writer->writeByte(intval((($note->getVelocity() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1));
    }

    if (($flags & 0x20) != 0) {
      $this->writer->writeByte($note->getValue());
    }

    $this->writer->skipBytes(1);

    if (($flags & 0x08) != 0) {
      $this->writer->getWriter('NoteEffect5Writer')->writeNoteEffects($effect);
    }
  }

  /**
   * Create flags
   * 
   * @param  \PhpTabs\Music\NoteEffect $effect
   * @return int
   */
  private function createFlags(NoteEffect $effect)
  {
    $flags = 0x20 | 0x10;

    if ($effect->isVibrato()
        || $effect->isBend()
        || $effect->isGrace() 
        || $effect->isSlide()
        || $effect->isHammer()
        || $effect->isLetRing()
        || $effect->isPalmMute()
        || $effect->isStaccato()
        || $effect->isHarmonic()
        || $effect->isTrill()
        || $effect->isTremoloPicking()
    ) {
      $flags |= 0x08;
    }

    if ($effect->isGhostNote()) {
      $flags |= 0x04;
    }

    if ($effect->isHeavyAccentuatedNote()) {
      $flags |= 0x02;
    }

    if ($effect->isAccentuatedNote()) {
      $flags |= 0x40;
    }

    return $flags;
  }
}
