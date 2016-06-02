<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectTremoloBar;
use PhpTabs\Model\EffectTremoloPicking;
use PhpTabs\Model\NoteEffect;

class GuitarPro4Effects extends AbstractReader
{
  /**
   * Reads tremolo bar
   * 
   * @param NoteEffect $noteEffect
   */
  public function readTremoloBar(NoteEffect $effect)
  {
    $tremoloBar = new EffectTremoloBar();

    $this->reader->skip(5);

    $points = $this->reader->readInt();

    for ($i = 0; $i < $points; $i++)
    {
      $position = $this->reader->readInt();
      $value = $this->reader->readInt();
      $this->reader->readByte();

      $pointPosition = round($position * EffectTremoloBar::MAX_POSITION_LENGTH / GuitarProReaderInterface::GP_BEND_POSITION);
      $pointValue = round($value / (GuitarProReaderInterface::GP_BEND_SEMITONE * 2));
      $tremoloBar->addPoint($pointPosition, $pointValue);
    }

    if(count($tremoloBar->getPoints()))
    {
      $effect->setTremoloBar($tremoloBar);
    }
  }

  /**
   * Reads tremolo picking
   * 
   * @param NoteEffect $noteEffect
   */
  public function readTremoloPicking(NoteEffect $noteEffect)
  {
    $value = $this->reader->readUnsignedByte();

    $tremoloPicking = new EffectTremoloPicking();

    if($value == 1)
    {
      $tremoloPicking->getDuration()->setValue(Duration::EIGHTH);
      $noteEffect->setTremoloPicking($tremoloPicking);
    }
    else if($value == 2)
    {
      $tremoloPicking->getDuration()->setValue(Duration::SIXTEENTH);
      $noteEffect->setTremoloPicking($tremoloPicking);
    }
    else if($value == 3)
    {
      $tremoloPicking->getDuration()->setValue(Duration::THIRTY_SECOND);
      $noteEffect->setTremoloPicking($tremoloPicking);
    }
  }
}
