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

use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\EffectTremoloPicking;
use PhpTabs\Music\EffectTrill;
use PhpTabs\Music\NoteEffect;

abstract class ExporterEffects
{
    /**
     * @param  \PhpTabs\Music\NoteEffect $effect
     * @return array
     */
    protected function exportEffect(NoteEffect $effect)
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
     * @param  null|\PhpTabs\Music\EffectBend $effect
     * @return null|array
     */
    protected function exportBend(EffectBend $effect = null)
    {
        return $this->exportTremoloBar($effect);
    }

    /**
     * @param  null|\PhpTabs\Music\EffectGrace $effect
     * @return null|array
     */
    protected function exportGrace(EffectGrace $effect= null)
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
     * @param  null|\PhpTabs\Music\EffectTremoloBar $effect
     * @return null|array
     */
    protected function exportTremoloBar($effect = null)
    {
        return !is_object($effect)
            ? null
            : ['points' => $this->exportPoints($effect->getPoints())];
    }

    /**
     * @param  null|\PhpTabs\Music\EffectHarmonic $effect
     * @return null|array
     */
    protected function exportHarmonic(EffectHarmonic $effect = null)
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
     * @param  null|\PhpTabs\Music\EffectTrill $effect
     * @return null|array
     */
    protected function exportTrill(EffectTrill $effect = null)
    {
        return is_object($effect) ? array(
            'fret'      => $effect->getFret(), 
            'duration'  => $this->exportDuration($effect->getDuration())
        ) : null;
    }

    /**
     * @param  null|\PhpTabs\Music\EffectTremoloPicking $effect
     * @return null|array
     */
    protected function exportTremoloPicking(EffectTremoloPicking $effect = null)
    {
        return is_object($effect) ? array(
            'duration'  => $this->exportDuration($effect->getDuration())
        ) : null;
    }

    /**
     * @param  array $points
     * @return array
     */
    protected function exportPoints(array $points)
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
