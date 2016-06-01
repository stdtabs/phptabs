<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectHarmonic;
use PhpTabs\Model\EffectTrill;
use PhpTabs\Model\NoteEffect;

class GuitarPro4NoteEffects extends AbstractReader
{
  /**
   * Reads NoteEffect
   * 
   * @param NoteEffect $noteEffect
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
      $this->reader->factory('GuitarPro4Effects')->readBend($noteEffect);
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
      $harmonic = new EffectHarmonic();
      $type = intval($this->reader->readByte());
      if($type == 1)
      {
        $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      }
      else if($type == 3)
      {
        $harmonic->setType(EffectHarmonic::TYPE_TAPPED);
      }
      else if($type == 4)
      {
        $harmonic->setType(EffectHarmonic::TYPE_PINCH);
      }
      else if($type == 5)
      {
        $harmonic->setType(EffectHarmonic::TYPE_SEMI);
      }
      else if($type == 15)
      {
        $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
        $harmonic->setData(2);
      }
      else if($type == 17)
      {
        $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
        $harmonic->setData(3);
      }
      else if($type == 22)
      {
        $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
        $harmonic->setData(0);
      }
      $noteEffect->setHarmonic($harmonic);
    }
    if (($flags2 & 0x20) != 0)
    {
      $fret = $this->reader->readByte();
      $period = $this->reader->readByte();
      $trill = new EffectTrill();
      $trill->setFret($fret);
      if($period == 1)
      {
        $trill->getDuration()->setValue(Duration::SIXTEENTH);
        $noteEffect->setTrill($trill);
      }
      else if($period == 2)
      {
        $trill->getDuration()->setValue(Duration::THIRTY_SECOND);
        $noteEffect->setTrill($trill);
      }
      else if($period == 3)
      {
        $trill->getDuration()->setValue(Duration::SIXTY_FOURTH);
        $noteEffect->setTrill($trill);
      }
    }
  }

}
