<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;

class GuitarProKeySignature
{
  private $reader;

  public function __construct(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

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
