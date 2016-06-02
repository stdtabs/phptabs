<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Beat;
use PhpTabs\Model\EffectHarmonic;
use PhpTabs\Model\NoteEffect;

class GuitarPro3BeatEffects extends AbstractReader
{
  /**
   * Reads beat effects
   * 
   * @param Beat $beat
   * @param NoteEffect $effect
   */
  public function readBeatEffects(Beat $beat, NoteEffect $effect)
  {
    $flags = $this->reader->readUnsignedByte();
    $effect->setVibrato((($flags & 0x01) != 0) || (($flags & 0x02) != 0));
    $effect->setFadeIn((($flags & 0x10) != 0));

    if (($flags & 0x20) != 0)
    {
      $type = $this->reader->readUnsignedByte();
      if ($type == 0)
      {
        $this->readTremoloBar($effect);
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
      $this->reader->factory('GuitarProStroke')->readStroke($beat);
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
