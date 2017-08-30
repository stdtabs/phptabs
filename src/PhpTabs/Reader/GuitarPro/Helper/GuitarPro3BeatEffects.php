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

use PhpTabs\Music\Beat;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\NoteEffect;

class GuitarPro3BeatEffects extends AbstractReader
{
  /**
   * Reads beat effects
   * 
   * @param \PhpTabs\Music\Beat $beat
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  public function readBeatEffects(Beat $beat, NoteEffect $effect)
  {
    $flags = $this->reader->readUnsignedByte();
    $effect->setVibrato(
      (($flags & 0x01) != 0) || (($flags & 0x02) != 0)
    );

    $effect->setFadeIn((($flags & 0x10) != 0));

    if (($flags & 0x20) != 0)
    {
      $type = $this->reader->readUnsignedByte();
      if ($type == 0)
      {
        $this->reader->factory('GuitarPro3Effects')->readTremoloBar($effect);
      }
      else
      {
        $effect->setTapping($type == 1);
        $effect->setSlapping($type == 2);
        $effect->setPopping($type == 3);
        $this->reader->readInt();
      }
    }

    if (($flags & 0x40) != 0)
    {
      $this->reader->factory('GuitarPro3Stroke')->readStroke($beat);
    }

    if (($flags & 0x04) != 0)
    {
      $harmonic = new EffectHarmonic();
      $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      $effect->setHarmonic($harmonic);
    }

    if (($flags & 0x08) != 0)
    {
      $harmonic = new EffectHarmonic();
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $harmonic->setData(0);
      $effect->setHarmonic($harmonic);
    }
  }
}
