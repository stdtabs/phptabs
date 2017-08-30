<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Dumper;

abstract class DumperEffects
{
  /**
   * @param \PhpTabs\Music\NoteEffect $effect
   * 
   * @return array
   */
  protected function dumpEffect($effect)
  {
    return array(
      'bend'            => $this->dumpBend($effect->getBend()),
      'tremoloBar'      => $this->dumpTremoloBar($effect->getTremoloBar()),
      'harmonic'        => $this->dumpHarmonic($effect->getHarmonic()),
      'grace'           => $this->dumpGrace($effect->getGrace()),
      'trill'           => $this->dumpTrill($effect->getTrill()),
      'tremoloPicking'  => $this->dumpTremoloPicking($effect->getTremoloPicking()),
      'vibrato'         => $effect->isVibrato(),
      'deadNote'        => $effect->isDeadNote(),
      'slide'           => $effect->isSlide(),
      'hammer'          => $effect->isHammer(),
      'ghostNote'       => $effect->isGhostNote(),
      'accentuatedNote' => $effect->isAccentuatedNote(),
      'heavyAccentuatedNote'  => $effect->isHeavyAccentuatedNote(),
      'palmMute'        => $effect->isPalmMute(),
      'staccato'        => $effect->isStaccato(),
      'tapping'         => $effect->isTapping(),
      'slapping'        => $effect->isSlapping(),
      'popping'         => $effect->isPopping(),
      'fadeIn'          => $effect->isFadeIn(),
      'letRing'         => $effect->isLetRing()
    );
  }

  /**
   * @param \PhpTabs\Music\EffectBend $effect
   * 
   * @return array
   */
  protected function dumpBend($effect)
  {
    return $this->dumpTremoloBar($effect);
  }

  /**
   * @param \PhpTabs\Music\EffectGrace $effect
   * 
   * @return array
   */
  protected function dumpGrace($effect)
  {
    return is_object($effect) ? array(
        'fret'       => $effect->getFret(), 
        'duration'   => $effect->getDuration(), 
        'dynamic'    => $effect->getDynamic(), 
        'transition' => $effect->getTransition(), 
        'onBeat'     => $effect->isOnBeat(), 
        'dead'       => $effect->isDead()
    ) : null;
  }

  /**
   * @param \PhpTabs\Model\EffectTremolo $effect
   * 
   * @return array
   */
  protected function dumpTremoloBar($effect)
  {
    return !is_object($effect) ? null
      : array('points' => $this->dumpPoints($effect->getPoints()));
  }

  /**
   * @param \PhpTabs\Music\EffectHarmonic $effect
   * 
   * @return array
   */
  protected function dumpHarmonic($effect)
  {
    return is_object($effect) ? array(
      'type'         => $effect->getType(), 
      'data'         => $effect->getData(), 
      'isNatural'    => $effect->isNatural(), 
      'isArtificial' => $effect->isArtificial(), 
      'isTapped'     => $effect->isTapped(), 
      'isPinch'      => $effect->isPinch(), 
      'isSemi'       => $effect->isSemi()
    ) : null;
  }

  /**
   * @param \PhpTabs\Music\EffectTrill $effect
   * 
   * @return array
   */
  protected function dumpTrill($effect)
  {
    return is_object($effect) ? array(
      'fret'      => $effect->getFret(), 
      'duration'  => $this->dumpDuration($effect->getDuration())
    ) : null;
  }

  /**
   * @param \PhpTabs\Music\EffectTremoloPicking $effect
   * 
   * @return array
   */
  protected function dumpTremoloPicking($effect)
  {
    return is_object($effect) ? array(
      'duration'  => $this->dumpDuration($effect->getDuration())
    ) : null;
  }

  /**
   * @param array $points
   * 
   * @return array
   */
  protected function dumpPoints($points)
  {
    $content = array();

    foreach ($points as $point)
    {
      $content[] = array(
        'position'  => $point->getPosition(), 
        'value'     => $point->getValue()
      );
    }

    return $content;
  }
}
