<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\NoteEffect;

class GuitarPro5NoteEffects extends AbstractReader
{
  /**
   * Reads note effects
   * 
   * @param NoteEffect $noteEffect
   */
  public function readNoteEffects(NoteEffect $noteEffect)
  {
    $flags1 = intval($this->reader->readUnsignedByte());
    $flags2 = intval($this->reader->readUnsignedByte());

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
      $this->reader->readArtificialHarmonic($noteEffect);
    }

    if (($flags2 & 0x20) != 0)
    {
      $this->reader->readTrill($noteEffect);
    }

    $noteEffect->setHammer((($flags1 & 0x02) != 0));
    $noteEffect->setLetRing((($flags1 & 0x08) != 0));
    $noteEffect->setVibrato((($flags2 & 0x40) != 0) || $noteEffect->isVibrato());
    $noteEffect->setPalmMute((($flags2 & 0x02) != 0));
    $noteEffect->setStaccato((($flags2 & 0x01) != 0));
  }
}
