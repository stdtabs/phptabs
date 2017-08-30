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

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\EffectTremoloBar;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Velocities;

class GuitarPro3Effects extends AbstractReader
{
  /**
   * Reads a note effect
   * 
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  public function readNoteEffects(NoteEffect $effect)
  {
    $flags = $this->reader->readUnsignedByte();
    $effect->setHammer( (($flags & 0x02) != 0) );
    $effect->setSlide( (($flags & 0x04) != 0) );
    $effect->setLetRing((($flags & 0x08) != 0));

    if (($flags & 0x01) != 0)
    {
      $this->readBend($effect, $this->reader);
    }

    if (($flags & 0x10) != 0)
    {
      $this->readGrace($effect, $this->reader);
    }
  }

  /**
   * Reads bend
   *
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  public function readBend(NoteEffect $effect)
  {
    $bend = new EffectBend();
    $this->reader->skip(5);
    $points = $this->reader->readInt();

    for ($i = 0; $i < $points; $i++)
    {
      $bendPosition = $this->reader->readInt();
      $bendValue = $this->reader->readInt();
      $this->reader->readByte(); //vibrato

      $pointPosition = round($bendPosition * EffectBend::MAX_POSITION_LENGTH / GuitarProReaderInterface::GP_BEND_POSITION);
      $pointValue = round($bendValue * EffectBend::SEMITONE_LENGTH / GuitarProReaderInterface::GP_BEND_SEMITONE);
      $bend->addPoint($pointPosition, $pointValue);
    }

    if (count($bend->getPoints()))
    {
      $effect->setBend($bend);
    }
  }

  /**
   * Reads grace
   * 
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  private function readGrace(NoteEffect $effect)
  {
    $fret = $this->reader->readUnsignedByte();
    $grace = new EffectGrace();
    $grace->setOnBeat(false);
    $grace->setDead( ($fret == 255) );
    $grace->setFret( ((!$grace->isDead()) ? $fret : 0) );
    $grace->setDynamic( (Velocities::MIN_VELOCITY + (Velocities::VELOCITY_INCREMENT * $this->reader->readUnsignedByte())) - Velocities::VELOCITY_INCREMENT );
    $transition = $this->reader->readUnsignedByte();

    if ($transition == 0)
    {
      $grace->setTransition(EffectGrace::TRANSITION_NONE);
    }
    elseif ($transition == 1)
    {
      $grace->setTransition(EffectGrace::TRANSITION_SLIDE);
    }
    elseif ($transition == 2)
    {
      $grace->setTransition(EffectGrace::TRANSITION_BEND);
    }
    elseif ($transition == 3)
    {
      $grace->setTransition(EffectGrace::TRANSITION_HAMMER);
    }

    $grace->setDuration($this->reader->readUnsignedByte());
    $effect->setGrace($grace);
  }

  /**
   * Reads tremolo bar
   * 
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  public function readTremoloBar(NoteEffect $noteEffect)
  {
    $value = $this->reader->readInt();
    $effect = new EffectTremoloBar();
    $effect->addPoint(0, 0);
    $effect->addPoint(round(EffectTremoloBar::MAX_POSITION_LENGTH / 2)
      , round( -($value / (GuitarProReaderInterface::GP_BEND_SEMITONE * 2))));
    $effect->addPoint(EffectTremoloBar::MAX_POSITION_LENGTH, 0);
    $noteEffect->setTremoloBar($effect);
  }
}
