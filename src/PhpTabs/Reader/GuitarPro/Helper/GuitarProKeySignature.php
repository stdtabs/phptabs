<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

class GuitarProKeySignature extends AbstractReader
{
  /**
   * Reads the key signature
   * 
   * 0: C 1: G, -1: F
   * @return integer Key signature
   */
  public function readKeySignature()
  {
    $keySignature = $this->reader->readByte();

    if ($keySignature < 0)
    {
      $keySignature = 7 - $keySignature;
    }

    return $keySignature;
  }
}
