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

use PhpTabs\Music\Note;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Track;
use PhpTabs\Music\Velocities;

class GuitarPro5Note extends AbstractReader
{
  /**
   * Reads a note
   * 
   * @param \PhpTabs\Music\TabString $string
   * @param \PhpTabs\Music\Track $track
   * @param \PhpTabs\Music\NoteEffect $effect
   *
   * @return \PhpTabs\Music\Note
   */
  public function readNote(TabString $string, Track $track, NoteEffect $effect)
  {
    $flags = $this->reader->readUnsignedByte();
    $note = new Note();
    $note->setString($string->getNumber());
    $note->setEffect($effect);
    $note->getEffect()->setAccentuatedNote((($flags & 0x40) != 0));
    $note->getEffect()->setHeavyAccentuatedNote((($flags & 0x02) != 0));
    $note->getEffect()->setGhostNote((($flags & 0x04) != 0));

    if (($flags & 0x20) != 0)
    {
      $noteType = $this->reader->readUnsignedByte();
      $note->setTiedNote($noteType == 0x02);
      $note->getEffect()->setDeadNote($noteType == 0x03);
    }

    if (($flags & 0x10) != 0)
    {
      $note->setVelocity(
        (Velocities::MIN_VELOCITY + (Velocities::VELOCITY_INCREMENT * $this->reader->readByte()))
        - Velocities::VELOCITY_INCREMENT
      );
    }

    if (($flags & 0x20) != 0)
    {
      $fret = $this->reader->readByte();

      $value = $note->isTiedNote() ? 
        $this->reader->factory('GuitarPro5TiedNote')->getTiedNoteValue($string->getNumber(), $track)
        : $fret;

      $note->setValue($value >= 0 && $value < 100 ? $value : 0);
    }

    if (($flags & 0x80) != 0)
    {
      $this->reader->skip(2);
    }

    if (($flags & 0x01) != 0)
    {
      $this->reader->skip(8);
    }
    
    $this->reader->skip();
    
    if (($flags & 0x08) != 0)
    {
      $this->reader->factory('GuitarPro5NoteEffects')->readNoteEffects($note->getEffect());
    }

    return $note;
  }
}
