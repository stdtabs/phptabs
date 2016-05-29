<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectBend;
use PhpTabs\Model\EffectTremoloBar;
use PhpTabs\Model\EffectTremoloPicking;
use PhpTabs\Model\NoteEffect;

class GuitarPro4Effects
{
  /**
   * Reads bend informations
   *
   * @param NoteEffect $effect
   */
  public function readBend(NoteEffect $effect, $reader)
  {
    $bend = new EffectBend();

    $reader->skip(5);

    $points = $reader->readInt();

    for ($i = 0; $i < $points; $i++)
    {
      $bendPosition = $reader->readInt();
      $bendValue = $reader->readInt();
      $reader->readByte();

      $pointPosition = round($bendPosition * EffectBend::MAX_POSITION_LENGTH / GuitarProReaderInterface::GP_BEND_POSITION);
      $pointValue = round($bendValue * EffectBend::SEMITONE_LENGTH / GuitarProReaderInterface::GP_BEND_SEMITONE);
      $bend->addPoint($pointPosition, $pointValue);
    }

    if(count($bend->getPoints()))
    {
      $effect->setBend($bend);
    }
  }

  /**
   * Reads tremolo bar
   * 
   * @param NoteEffect $noteEffect
   */
  public function readTremoloBar(NoteEffect $effect, $reader)
  {
    $tremoloBar = new EffectTremoloBar();

    $reader->skip(5);

    $points = $reader->readInt();

    for ($i = 0; $i < $points; $i++)
    {
      $position = $reader->readInt();
      $value = $reader->readInt();
      $reader->readByte();

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
  public function readTremoloPicking(NoteEffect $noteEffect, $reader)
  {
    $value = $reader->readUnsignedByte();

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
