<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Exporter;

abstract class ExporterEffects
{
  /**
   * @param \PhpTabs\Music\NoteEffect $effect
   * 
   * @return array
   */
  protected function exportEffect($effect)
  {
    return array(
      'bend'            => $this->exportBend($effect->getBend()),
      'tremoloBar'      => $this->exportTremoloBar($effect->getTremoloBar()),
      'harmonic'        => $this->exportHarmonic($effect->getHarmonic()),
      'grace'           => $this->exportGrace($effect->getGrace()),
      'trill'           => $this->exportTrill($effect->getTrill()),
      'tremoloPicking'  => $this->exportTremoloPicking($effect->getTremoloPicking()),
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
  protected function exportBend($effect)
  {
    return $this->exportTremoloBar($effect);
  }

  /**
   * @param \PhpTabs\Music\EffectGrace $effect
   * 
   * @return array
   */
  protected function exportGrace($effect)
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
   * @param \PhpTabs\Music\EffectTremoloBar $effect
   * 
   * @return array
   */
  protected function exportTremoloBar($effect)
  {
    return !is_object($effect) ? null
      : array('points' => $this->exportPoints($effect->getPoints()));
  }

  /**
   * @param \PhpTabs\Music\EffectHarmonic $effect
   * 
   * @return array
   */
  protected function exportHarmonic($effect)
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
  protected function exportTrill($effect)
  {
    return is_object($effect) ? array(
      'fret'      => $effect->getFret(), 
      'duration'  => $this->exportDuration($effect->getDuration())
    ) : null;
  }

  /**
   * @param \PhpTabs\Music\EffectTremoloPicking $effect
   * 
   * @return array
   */
  protected function exportTremoloPicking($effect)
  {
    return is_object($effect) ? array(
      'duration'  => $this->exportDuration($effect->getDuration())
    ) : null;
  }

  /**
   * @param array $points
   * 
   * @return array
   */
  protected function exportPoints($points)
  {
    return array_reduce(
      $points,
      function ($carry, $point) {
        $carry[] = array(
          'position'  => $point->getPosition(), 
          'value'     => $point->getValue()
        );
        return $carry;
      },
      []
    );
  }
}
