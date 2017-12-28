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

use PhpTabs\Music\Tempo;

class GuitarPro4MixChange extends AbstractReader
{
  /**
   * Reads mix change informations
   * 
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function readMixChange(Tempo $tempo)
  {
    $this->reader->readByte(); //instrument
    
    $criterias = ['volume', 'pan', 'chorus', 'reverb', 'phaser', 'tremolo'];
    
    foreach ($criterias as $name) {
      $$name = $this->reader->readByte();
    }

    $tempoValue = $this->reader->readInt();

    foreach ($criterias as $name) {
      if ($$name >= 0) {
        $this->reader->readByte();
      }
    }

    if ($tempoValue >= 0) {
      $tempo->setValue($tempoValue);
      $this->reader->readByte();
    }

    $this->reader->readByte();
  }
}
