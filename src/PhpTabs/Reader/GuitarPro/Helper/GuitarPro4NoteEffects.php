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
use PhpTabs\Music\EffectTrill;
use PhpTabs\Music\NoteEffect;

class GuitarPro4NoteEffects extends AbstractReader
{
  /**
   * Reads NoteEffect
   * 
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  public function readNoteEffects(NoteEffect $noteEffect)
  {
    $flags1 = intval($this->reader->readUnsignedByte());
    $flags2 = intval($this->reader->readUnsignedByte());
    $noteEffect->setHammer((($flags1 & 0x02) != 0));
    $noteEffect->setLetRing((($flags1 & 0x08) != 0));
    $noteEffect->setVibrato((($flags2 & 0x40) != 0) || $noteEffect->isVibrato());
    $noteEffect->setPalmMute((($flags2 & 0x02) != 0));
    $noteEffect->setStaccato((($flags2 & 0x01) != 0));

    if (($flags1 & 0x01) != 0)
    {
      $this->reader->factory('GuitarPro3Effects')->readBend($noteEffect);
    }

    if (($flags1 & 0x10) != 0)
    {
      $this->reader->readGrace($noteEffect);
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
      $this->readHarmonic($noteEffect);
    }

    if (($flags2 & 0x20) != 0)
    {
      $this->readTrill($noteEffect);
    }
  }

  /**
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  public function readTrill(NoteEffect $noteEffect)
  {
    $fret = $this->reader->readByte();
    $period = $this->reader->readByte();

    $trill = new EffectTrill();
    $trill->setFret($fret);

    if ($period == 1)
    {
      $trill->getDuration()->setValue(Duration::SIXTEENTH);
      $noteEffect->setTrill($trill);
    }
    elseif ($period == 2)
    {
      $trill->getDuration()->setValue(Duration::THIRTY_SECOND);
      $noteEffect->setTrill($trill);
    }
    elseif ($period == 3)
    {
      $trill->getDuration()->setValue(Duration::SIXTY_FOURTH);
      $noteEffect->setTrill($trill);
    }
  }

  /**
   * @param \PhpTabs\Music\NoteEffect $noteEffect
   */
  private function readHarmonic(NoteEffect $noteEffect)
  {
    $harmonic = new EffectHarmonic();
    $type = intval($this->reader->readByte());

    if ($type == 1)
    {
      $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
    }
    elseif ($type == 3)
    {
      $harmonic->setType(EffectHarmonic::TYPE_TAPPED);
    }
    elseif ($type == 4)
    {
      $harmonic->setType(EffectHarmonic::TYPE_PINCH);
    }
    elseif ($type == 5)
    {
      $harmonic->setType(EffectHarmonic::TYPE_SEMI);
    }
    elseif ($type == 15)
    {
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $harmonic->setData(2);
    }
    elseif ($type == 17)
    {
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $harmonic->setData(3);
    }
    elseif ($type == 22)
    {
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $harmonic->setData(0);
    }

    $noteEffect->setHarmonic($harmonic);
  }
}
