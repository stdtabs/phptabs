<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro\Writers;

use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\EffectTremoloBar;
use PhpTabs\Music\EffectTremoloPicking;
use PhpTabs\Music\EffectTrill;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Velocities;
use PhpTabs\Writer\GuitarProWriterBase;
use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface as GprInterface;

class NoteEffectWriter
{
  private $writer;

  public function __construct($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @param \PhpTabs\Music\NoteEffect $effect
   */
  public function writeNoteEffects(NoteEffect $effect)
  {
    $flags1 = $this->parseFlag1($effect);
    $flags2 = $this->parseFlag2($effect);

    $this->writer->writeUnsignedByte($flags1);
    $this->writer->writeUnsignedByte($flags2);

    if (($flags1 & 0x01) != 0) {
      $this->writeBend($effect->getBend());
    }

    if (($flags1 & 0x10) != 0) {
      $this->writeGrace($effect->getGrace());
    }

    if (($flags2 & 0x04) != 0) {
      $this->writeTremoloPicking($effect->getTremoloPicking());
    }

    if (($flags2 & 0x08) != 0) {
      $this->writer->writeByte(1);
    }

    if (($flags2 & 0x10) != 0) {

      switch ($effect->getHarmonic()->getType()) {
        case EffectHarmonic::TYPE_NATURAL:
          $this->writer->writeByte(1);
          break;
        case EffectHarmonic::TYPE_TAPPED:
          $this->writer->writeByte(3);
          break;
        case EffectHarmonic::TYPE_PINCH:
          $this->writer->writeByte(4);
          break;
        case EffectHarmonic::TYPE_SEMI:
          $this->writer->writeByte(5);
          break;
        case EffectHarmonic::TYPE_ARTIFICIAL:
          $this->writer->writeByte(15);
          break;
      }
    }

    if (($flags2 & 0x20) != 0) {

      $this->writer->writeByte($effect->getTrill()->getFret());

      switch ($effect->getTrill()->getDuration()->getValue()) {
        case Duration::SIXTEENTH:
          $this->writer->writeByte(1);
          break;
        case Duration::THIRTY_SECOND:
          $this->writer->writeByte(2);
          break;
        case Duration::SIXTY_FOURTH:
          $this->writer->writeByte(3);
          break;
      }
    }
  }

  /**
   * Parse flag 1 for GuitarPro 4
   * 
   * @param \PhpTabs\Music\NoteEffect $effect
   * @return int
   */
  public function parseFlag1(NoteEffect $effect)
  {
    $flags1 = 0;

    if ($effect->isBend()) {
      $flags1 |= 0x01;
    }

    if ($effect->isHammer()) {
      $flags1 |= 0x02;
    }

    if ($effect->isLetRing()) {
      $flags1 |= 0x08;
    }

    if ($effect->isGrace()) {
      $flags1 |= 0x10;
    }

    return $flags1;
  }

  /**
   * Parse flag 2 for GuitarPro 4, 5
   * 
   * @param \PhpTabs\Music\NoteEffect $effect
   * @return int
   */
  public function parseFlag2(NoteEffect $effect)
  {
    $flags2 = 0;

    if ($effect->isStaccato()) {
      $flags2 |= 0x01;
    }

    if ($effect->isPalmMute()) {
      $flags2 |= 0x02;
    }

    if ($effect->isTremoloPicking()) {
      $flags2 |= 0x04;
    }

    if ($effect->isSlide()) {
      $flags2 |= 0x08;
    }

    if ($effect->isHarmonic()) {
      $flags2 |= 0x10;
    }

    if ($effect->isTrill()) {
      $flags2 |= 0x20;
    }

    if ($effect->isVibrato()) {
      $flags2 |= 0x40;
    }

    return $flags2;
  }

  /**
   * @param \PhpTabs\Music\EffectBend $bend
   */
  public function writeBend(EffectBend $bend)
  {
    $points = count($bend->getPoints());
    $this->writer->writeByte(1);
    $this->writer->writeInt(0);
    $this->writer->writeInt($points);

    for ($i = 0; $i < $points; $i++) {
      $point = $bend->getPoints()[$i];
      $this->writer->writeInt(
        intval($point->getPosition() * GprInterface::GP_BEND_POSITION / EffectBend::MAX_POSITION_LENGTH)
      );
      $this->writer->writeInt(
        intval($point->getValue() * GprInterface::GP_BEND_SEMITONE / EffectBend::SEMITONE_LENGTH)
      );
      $this->writer->writeByte(0);
    }
  }

  /**
   * @param \PhpTabs\Music\EffectGrace $grace
   */
  public function writeGrace(EffectGrace $grace)
  {
    if ($grace->isDead()) {
      $this->writer->writeUnsignedByte(0xff);
    } else {
      $this->writer->writeUnsignedByte($grace->getFret());
    }

    $this->writer->writeUnsignedByte(
      intval((($grace->getDynamic() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1)
    );

    $this->writeTransition(
      $grace->getTransition()
    );

    $this->writer->writeUnsignedByte($grace->getDuration());
  }

  /**
   * Write a Grace transition byte
   * 
   * @param int $transition
   */
  public function writeTransition($transition)
  {
    switch ($transition) {
      case EffectGrace::TRANSITION_NONE:
        $this->writer->writeUnsignedByte(0);
        break;
      case EffectGrace::TRANSITION_SLIDE:
        $this->writer->writeUnsignedByte(1);
        break;
      case EffectGrace::TRANSITION_BEND:
        $this->writer->writeUnsignedByte(2);
        break;
      case EffectGrace::TRANSITION_HAMMER:
        $this->writer->writeUnsignedByte(3);
        break;
    }
  }

  /**
   * @param \PhpTabs\Music\EffectTremoloBar $effect
   */
  public function writeTremoloBar(EffectTremoloBar $effect)
  {
    $points = $effect->getPoints();

    switch(str_replace('PhpTabs\\Writer\\GuitarPro\\', '', get_class($this->writer))) {
      case 'GuitarPro5Writer':
        $this->writer->writeByte(1);
        break;
      default:
        $this->writer->writeByte(6);
        break;
    }
    
    $this->writer->writeInt(0);
    $this->writer->writeInt(count($points));

    foreach ($points as $point) {
      $this->writer->writeInt($point->getPosition() * GprInterface::GP_BEND_POSITION / EffectTremoloBar::MAX_POSITION_LENGTH);
      $this->writer->writeInt($point->getValue() * GprInterface::GP_BEND_SEMITONE * 2);
      $this->writer->writeByte(0);
    }
  }

  /**
   * @param \PhpTabs\Music\EffectTremoloPicking $effect
   */
  public function writeTremoloPicking(EffectTremoloPicking $effect)
  {
    switch ($effect->getDuration()->getValue()) {
      case Duration::EIGHTH:
        $this->writer->writeUnsignedByte(1);
        break;
      case Duration::SIXTEENTH:
        $this->writer->writeUnsignedByte(2);
        break;
      case Duration::THIRTY_SECOND:
        $this->writer->writeUnsignedByte(3);
        break;
    }
  }

  /**
   * @param \PhpTabs\Music\EffectTrill $effect
   */
  public function writeTrill(EffectTrill $effect)
  {
    $this->writer->writeByte($effect->getFret());

    switch ($effect->getDuration()->getValue()) {
      case Duration::SIXTEENTH:
        $this->writer->writeByte(1);
        break;
      case Duration::THIRTY_SECOND:
        $this->writer->writeByte(2);
        break;
      case Duration::SIXTY_FOURTH:
        $this->writer->writeByte(3);
        break;
    }
  }
}
