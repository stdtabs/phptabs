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
