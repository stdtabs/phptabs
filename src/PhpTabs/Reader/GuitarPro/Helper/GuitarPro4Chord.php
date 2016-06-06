<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Chord;

class GuitarPro4Chord extends AbstractReader
{
  /**
   * Reads Chord informations
   * 
   * @param integer $strings
   * @param Beat $beat
   */
  public function readChord($strings, Beat $beat)
  {
    $chord = new Chord($strings);

    if (($this->reader->readUnsignedByte() & 0x01) == 0)
    {
      $chord->setName($this->reader->readStringByteSizeOfInteger());
      $chord->setFirstFret($this->reader->readInt());

      if($chord->getFirstFret() != 0)
      {
        for ($i = 0; $i < 6; $i++)
        {
          $this->readFret($chord, $i);
        }
      }
    }
    else
    {
      $this->reader->skip(16);
      $chord->setName($this->reader->readStringByte(21));
      $this->reader->skip(4);
      $chord->setFirstFret($this->reader->readInt());

      for ($i = 0; $i < 7; $i++)
      {
        $this->readFret($chord, $i);
      }
    
      $this->reader->skip(32);
    }

    if($chord->countNotes() > 0)
    {
      $beat->setChord($chord);
    }
  }

  private function readFret(Chord $chord, $index)
  {
    $fret = $this->reader->readInt();

    if($index < $chord->countStrings())
    {
      $chord->addFretValue($index, $fret);
    }
  }
}
