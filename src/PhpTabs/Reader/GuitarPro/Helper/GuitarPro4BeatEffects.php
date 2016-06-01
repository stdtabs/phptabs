<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Beat;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Stroke;

class GuitarPro4BeatEffects extends AbstractReader
{
  /**
   * Reads some NoteEffect informations
   * 
   * @param Beat $beat
   * @param NoteEffect $effect
   */
  public function readBeatEffects(Beat $beat, NoteEffect $noteEffect)
  {
    $flags1 = $this->reader->readUnsignedByte();
    $flags2 = $this->reader->readUnsignedByte();
    $noteEffect->setFadeIn((($flags1 & 0x10) != 0));
    $noteEffect->setVibrato((($flags1  & 0x02) != 0));

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
      $strokeDown = $this->reader->readByte();
      $strokeUp = $this->reader->readByte();

      if($strokeDown > 0 )
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_DOWN);
        $beat->getStroke()->setValue($this->reader->factory('GuitarPro3Effects')->toStrokeValue($strokeDown));
      }
      else if($strokeUp > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_UP);
        $beat->getStroke()->setValue($this->reader->factory('GuitarPro3Effects')->toStrokeValue($strokeUp));
      }
    }

    if (($flags2 & 0x02) != 0)
    {
      $this->reader->readByte();
    }
  }
}
