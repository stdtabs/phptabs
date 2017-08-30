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
use PhpTabs\Music\NoteEffect;

class GuitarPro4BeatEffects extends AbstractReader
{
  /**
   * Reads some beat effects
   * 
   * @param \PhpTabs\Music\Beat $beat
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  public function readBeatEffects(Beat $beat, NoteEffect $noteEffect)
  {
    $flags1 = $this->reader->readUnsignedByte();
    $flags2 = $this->reader->readUnsignedByte();
    $noteEffect->setFadeIn((($flags1 & 0x10) != 0));
    $noteEffect->setVibrato((($flags1 & 0x02) != 0));

    if (($flags1 & 0x20) != 0)
    {
      $effect = $this->reader->readUnsignedByte();
      $noteEffect->setTapping($effect == 1);
      $noteEffect->setSlapping($effect == 2);
      $noteEffect->setPopping($effect == 3);
    }

    if (($flags2 & 0x04) != 0)
    {
      $this->reader->factory('GuitarPro4Effects')->readTremoloBar($noteEffect);
    }

    if (($flags1 & 0x40) != 0)
    {
      $factory = $this->getParserName() == 'GuitarPro5'
        ? 'GuitarPro5' : 'GuitarPro3';
      $this->reader->factory($factory . 'Stroke')->readStroke($beat);
    }

    if (($flags2 & 0x02) != 0)
    {
      $this->reader->readByte();
    }
  }
}
