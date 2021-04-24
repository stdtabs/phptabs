<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Exporter;

use PhpTabs\Music\{
    Duration, EffectBend, EffectGrace, EffectHarmonic,
    EffectTremoloPicking, EffectTrill, NoteEffect
};

abstract class ExporterEffects
{
    /**
     * Export a note effect as an array
     */
    protected function exportEffect(NoteEffect $effect): array
    {
        return [
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
        ];
    }

    /**
     * Export a bend effect as an array
     */
    protected function exportBend(EffectBend $effect = null): ?array
    {
        return $this->exportTremoloBar($effect);
    }

    /**
     * Export a grace effect as an array
     */
    protected function exportGrace(EffectGrace $effect= null): ?array
    {
        return is_object($effect) ? [
            'fret'       => $effect->getFret(),
            'duration'   => $effect->getDuration(),
            'dynamic'    => $effect->getDynamic(),
            'transition' => $effect->getTransition(),
            'onBeat'     => $effect->isOnBeat(),
            'dead'       => $effect->isDead()
        ] : null;
    }

    /**
     * Export a tremolo bar as an array
     *
     * @param EffectBend|\PhpTabs\Music\EffectTremoloBar|null $effect
     */
    protected function exportTremoloBar($effect = null): ?array
    {
        return !is_object($effect)
            ? null
            : [
                'points' => $this->exportPoints($effect->getPoints())
            ];
    }

    /**
     * Export an harmonic effect as an array
     */
    protected function exportHarmonic(EffectHarmonic $effect = null): ?array
    {
        return is_object($effect) ? [
            'type'         => $effect->getType(),
            'data'         => $effect->getData(),
            'isNatural'    => $effect->isNatural(),
            'isArtificial' => $effect->isArtificial(),
            'isTapped'     => $effect->isTapped(),
            'isPinch'      => $effect->isPinch(),
            'isSemi'       => $effect->isSemi()
        ] : null;
    }

    /**
     * Export a trill as an array
     */
    protected function exportTrill(EffectTrill $effect = null): ?array
    {
        return is_object($effect) ? [
            'fret'      => $effect->getFret(),
            'duration'  => $this->exportDuration($effect->getDuration())
        ] : null;
    }

    /**
     * Export a tremolo pocking as an array
     */
    protected function exportTremoloPicking(EffectTremoloPicking $effect = null): ?array
    {
        return is_object($effect) ? [
            'duration'  => $this->exportDuration($effect->getDuration())
        ] : null;
    }

    /**
     * Export an array of points as an array (Formatted for export)
     */
    protected function exportPoints(array $points): array
    {
        return array_reduce(
            $points,
            function ($carry, $point) {
                $carry[] = [
                    'position'  => $point->getPosition(),
                    'value'     => $point->getValue()
                ];
                return $carry;
            },
            []
        );
    }

    abstract protected function exportDuration(Duration $duration): array;
}
