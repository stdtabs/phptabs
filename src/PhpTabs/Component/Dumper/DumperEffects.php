<?php

namespace PhpTabs\Component\Dumper;

/**
 * Helpers for Dumper
 */
abstract class DumperEffects
{
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

  protected function dumpBend($effect)
  {
    return $this->dumpTremoloBar($effect);
  }

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

  protected function dumpTremoloBar($effect)
  {
    return !is_object($effect) ? null
      : array('points' => $this->dumpPoints($effect->getPoints()));
  }

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

  protected function dumpTrill($effect)
  {
    return is_object($effect) ? array(
      'fret'      => $effect->getFret(), 
      'duration'  => $this->dumpDuration($effect->getDuration())
    ) : null;
  }

  protected function dumpTremoloPicking($effect)
  {
    return is_object($effect) ? array(
      'duration'  => $this->dumpDuration($effect->getDuration())
    ) : null;
  }

  protected function dumpPoints($points)
  {
    $content = array();

    foreach($points as $point)
    {
      $content[] = array(
        'position'  => $point->getPosition(), 
        'value'     => $point->getValue()
      );
    }

    return $content;
  }
}
