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

use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Velocities;

class GuitarPro5NoteEffects extends AbstractReader
{
  /**
   * Reads note effects
   * 
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  public function readNoteEffects(NoteEffect $noteEffect)
  {
    $flags1 = $this->reader->readUnsignedByte();
    $flags2 = $this->reader->readUnsignedByte();

    if (($flags1 & 0x01) != 0)
    {
      $this->reader->factory('GuitarPro3Effects')->readBend($noteEffect);
    }

    if (($flags1 & 0x10) != 0)
    {
      $this->readGrace($noteEffect);
    }

    if (($flags2 & 0x04) != 0)
    {
      $this->reader->factory('GuitarPro4Effects')->readTremoloPicking($noteEffect);
    }

    if (($flags2 & 0x08) != 0)
    {
      $noteEffect->setSlide(true);
      $this->reader->readByte();
    }

    if (($flags2 & 0x10) != 0)
    {
      $this->readArtificialHarmonic($noteEffect);
    }

    if (($flags2 & 0x20) != 0)
    {
      $this->reader->factory('GuitarPro4NoteEffects')->readTrill($noteEffect);
    }

    $noteEffect->setHammer((($flags1 & 0x02) != 0));
    $noteEffect->setLetRing((($flags1 & 0x08) != 0));
    $noteEffect->setVibrato((($flags2 & 0x40) != 0) || $noteEffect->isVibrato());
    $noteEffect->setPalmMute((($flags2 & 0x02) != 0));
    $noteEffect->setStaccato((($flags2 & 0x01) != 0));
  }

  /**
   * Reads an artificial harmonic
   * 
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  private function readArtificialHarmonic(NoteEffect $effect)
  {
    $type = $this->reader->readByte();
    $harmonic = new EffectHarmonic();
    $harmonic->setData(0);

    if ($type == 1)
    {
      $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      $effect->setHarmonic($harmonic);
    }
    elseif ($type == 2)
    {
      $this->reader->skip(3);
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $effect->setHarmonic($harmonic);
    }
    elseif ($type == 3)
    {
      $this->reader->skip(1);
      $harmonic->setType(EffectHarmonic::TYPE_TAPPED);
      $effect->setHarmonic($harmonic);
    }
    elseif ($type == 4)
    {
      $harmonic->setType(EffectHarmonic::TYPE_PINCH);
      $effect->setHarmonic($harmonic);
    }
    elseif ($type == 5)
    {
      $harmonic->setType(EffectHarmonic::TYPE_SEMI);
      $effect->setHarmonic($harmonic);
    }
  }

  /**
   * Reads EffectGrace
   * 
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  private function readGrace(NoteEffect $effect)
  {
    $fret = $this->reader->readUnsignedByte();
    $dynamic = $this->reader->readUnsignedByte();
    $transition = $this->reader->readByte();
    $duration = $this->reader->readUnsignedByte();
    $flags = $this->reader->readUnsignedByte();

    $grace = new EffectGrace();
    $grace->setFret($fret);
    $grace->setDynamic((Velocities::MIN_VELOCITY 
      + (Velocities::VELOCITY_INCREMENT * $dynamic))
      - Velocities::VELOCITY_INCREMENT);
    $grace->setDuration($duration);
    $grace->setDead(($flags & 0x01) != 0);
    $grace->setOnBeat(($flags & 0x02) != 0);

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

    $effect->setGrace($grace);
  }
}
