<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;
use PhpTabs\Model\Beat;
use PhpTabs\Model\Duration;
use PhpTabs\Model\EffectBend;
use PhpTabs\Model\EffectGrace;
use PhpTabs\Model\EffectHarmonic;
use PhpTabs\Model\EffectTremoloBar;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Stroke;
use PhpTabs\Model\Velocities;

class GuitarPro3Effects
{
  private $reader;

  public function __construct(GuitarProReaderInterface $reader)
  {
    $this->reader = $reader;
  }

  /**
   * Reads beat effects
   * 
   * @param Beat $beat
   * @param NoteEffect $effect
   */
  public function readBeatEffects(Beat $beat, NoteEffect $effect)
  {
    $flags = $this->reader->readUnsignedByte();
    $effect->setVibrato((($flags & 0x01) != 0) || (($flags & 0x02) != 0));
    $effect->setFadeIn((($flags & 0x10) != 0));
    if (($flags & 0x20) != 0)
    {
      $type = $this->reader->readUnsignedByte();
      if ($type == 0)
      {
        $this->readTremoloBar($effect);
      }
      else
      {
        $effect->setTapping($type == 1);
        $effect->setSlapping($type == 2);
        $effect->setPopping($type == 3);
        $this->reader->readInt();
      }
    }
    if (($flags & 0x40) != 0)
    {
      $strokeDown = $this->reader->readByte();
      $strokeUp = $this->reader->readByte();
      if($strokeDown > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_DOWN );
        $beat->getStroke()->setValue($this->toStrokeValue($strokeDown));
      }
      else if($strokeUp > 0)
      {
        $beat->getStroke()->setDirection(Stroke::STROKE_UP);
        $beat->getStroke()->setValue($this->toStrokeValue($strokeUp));
      }
    }
    if (($flags & 0x04) != 0)
    {
      $harmonic = new EffectHarmonic();
      $harmonic->setType(EffectHarmonic::TYPE_NATURAL);
      $effect->setHarmonic($harmonic);
    }
    if (($flags & 0x08) != 0)
    {
      $harmonic = new EffectHarmonic();
      $harmonic->setType(EffectHarmonic::TYPE_ARTIFICIAL);
      $harmonic->setData(0);
      $effect->setHarmonic($harmonic);
    }
  }

  /**
   * Reads NoteEffect
   * 
   * @param NoteEffect $noteEffect
   * @return void
   */
  public function readNoteEffects(NoteEffect $effect)
  {
    $flags = $this->reader->readUnsignedByte();
    $effect->setHammer( (($flags & 0x02) != 0) );
    $effect->setSlide( (($flags & 0x04) != 0) );
    $effect->setLetRing((($flags & 0x08) != 0));
    if (($flags & 0x01) != 0)
    {
      $this->readBend($effect, $this->reader);
    }
    if (($flags & 0x10) != 0)
    {
      $this->readGrace($effect, $this->reader);
    }
  }

  /**
   * Reads bend
   *
   * @param NoteEffect $effect
   */
  private function readBend(NoteEffect $effect)
  {
    $bend = new EffectBend();
    $this->reader->skip(5);
    $points = $this->reader->readInt();
    for ($i = 0; $i < $points; $i++)
    {
      $bendPosition = $this->reader->readInt();
      $bendValue = $this->reader->readInt();
      $this->reader->readByte(); //vibrato

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
   * Reads grace
   * 
   * @param NoteEffect $effect
   */
  private function readGrace(NoteEffect $effect)
  {
    $fret = $this->reader->readUnsignedByte();
    $grace = new EffectGrace();
    $grace->setOnBeat(false);
    $grace->setDead( ($fret == 255) );
    $grace->setFret( ((!$grace->isDead()) ? $fret : 0) );
    $grace->setDynamic( (Velocities::MIN_VELOCITY + (Velocities::VELOCITY_INCREMENT * $this->reader->readUnsignedByte())) - Velocities::VELOCITY_INCREMENT );
    $transition = $this->reader->readUnsignedByte();
    if($transition == 0)
    {
      $grace->setTransition(EffectGrace::TRANSITION_NONE);
    }
    else if($transition == 1)
    {
      $grace->setTransition(EffectGrace::TRANSITION_SLIDE);
    }
    else if($transition == 2)
    {
      $grace->setTransition(EffectGrace::TRANSITION_BEND);
    }
    else if($transition == 3)
    {
      $grace->setTransition(EffectGrace::TRANSITION_HAMMER);
    }
    $grace->setDuration($this->reader->readUnsignedByte());
    $effect->setGrace($grace);
  }

  /**
   * Reads tremolo bar
   * 
   * @param NoteEffect $noteEffect
   */
  private function readTremoloBar(NoteEffect $noteEffect)
  {
    $value = $this->reader->readInt();
    $effect = new EffectTremoloBar();
    $effect->addPoint(0, 0);
    $effect->addPoint(round(EffectTremoloBar::MAX_POSITION_LENGTH / 2)
      , round( -($value / (GuitarProReaderInterface::GP_BEND_SEMITONE * 2))));
    $effect->addPoint(EffectTremoloBar::MAX_POSITION_LENGTH, 0);
    $noteEffect->setTremoloBar($effect);
  }

	/**
   * Get stroke value
   * 
   * @param integer $value
   * @return integer stroke value
   */
  public function toStrokeValue($value)
  {
    if($value == 1 || $value == 2)
    {
      return Duration::SIXTY_FOURTH;
    }
    if($value == 3)
    {
      return Duration::THIRTY_SECOND;
    }
    if($value == 4)
    {
      return Duration::SIXTEENTH;
    }
    if($value == 5)
    {
      return Duration::EIGHTH;
    }
    if($value == 6)
    {
      return Duration::QUARTER;
    }

    return Duration::SIXTY_FOURTH;
  }
}
