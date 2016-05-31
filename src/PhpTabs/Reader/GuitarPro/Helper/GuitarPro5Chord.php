<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Chord;

class GuitarPro5Chord extends AbstractReader
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
    $this->reader->skip(17);
    $chord->setName($this->reader->readStringByte(21));
    $this->reader->skip(4);
    $chord->setFirstFret($this->reader->readInt());

    for ($i = 0; $i < 7; $i++)
    {
      $fret = $this->reader->readInt();
      if($i < $chord->countStrings())
      {
        $chord->addFretValue($i, $fret);
      }
    }

    $this->reader->skip(32);
    if($chord->countNotes() > 0)
    {
      $beat->setChord($chord);
    }
  }
}
