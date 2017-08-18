<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Tempo;

class GuitarPro3Mixchange extends AbstractReader
{
  /**
   * Reads mix change informations
   * 
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function readMixChange(Tempo $tempo)
  {
    $this->reader->readByte(); //instrument
    $volume = $this->reader->readByte();
    $pan = $this->reader->readByte();
    $chorus = $this->reader->readByte();
    $reverb = $this->reader->readByte();
    $phaser = $this->reader->readByte();
    $tremolo = $this->reader->readByte();
    $tempoValue = $this->reader->readInt();

    if ($volume >= 0)
    {
      $this->reader->readByte();
    }

    if ($pan >= 0)
    {
      $this->reader->readByte();
    }

    if ($chorus >= 0)
    {
      $this->reader->readByte();
    }

    if ($reverb >= 0)
    {
      $this->reader->readByte();
    }

    if ($phaser >= 0)
    {
      $this->reader->readByte();
    }

    if ($tremolo >= 0)
    {
      $this->reader->readByte();
    }

    if ($tempoValue >= 0)
    {
      $tempo->setValue($tempoValue);
      $this->reader->readByte();
    }
  }
}
