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

namespace PhpTabs\Music;

abstract class NoteEffectBase
{
    /**
     * @var bool
     */
    protected $vibrato = false;

    /**
     * @var bool
     */
    protected $deadNote = false;

    /**
     * @var bool
     */
    protected $slide = false;

    /**
     * @var bool
     */
    protected $hammer = false;

    /**
     * @var bool
     */
    protected $ghostNote = false;

    /**
     * @var bool
     */
    protected $palmMute = false;

    /**
     * @var bool
     */
    protected $staccato = false;

    /**
     * @var bool
     */
    protected $tapping = false;

    /**
     * @var bool
     */
    protected $slapping = false;

    /**
     * @var bool
     */
    protected $popping = false;

    /**
     * @var bool
     */
    protected $fadeIn = false;

    /**
     * @var bool
     */
    protected $letRing = false;

    /**
     * @var bool
     */
    protected $accentuatedNote = false;

    /**
     * @var bool
     */
    protected $heavyAccentuatedNote = false;

    public function isDeadNote(): bool
    {
        return $this->deadNote;
    }

    public function isVibrato(): bool
    {
        return $this->vibrato;
    }

    public function isBend(): bool
    {
        return $this->bend !== null
            && $this->bend->countPoints();
    }

    public function isTremoloBar(): bool
    {
        return $this->tremoloBar !== null;
    }

    public function isTrill(): bool
    {
        return $this->trill !== null;
    }

    public function isTremoloPicking(): bool
    {
        return $this->tremoloPicking !== null;
    }

    public function isHammer(): bool
    {
        return $this->hammer;
    }

    public function isSlide(): bool
    {
        return $this->slide;
    }

    public function isGhostNote(): bool
    {
        return $this->ghostNote;
    }

    public function isAccentuatedNote(): bool
    {
        return $this->accentuatedNote;
    }

    public function isHeavyAccentuatedNote(): bool
    {
        return $this->heavyAccentuatedNote;
    }

    public function isHarmonic(): bool
    {
        return $this->harmonic !== null;
    }

    public function isGrace(): bool
    {
        return $this->grace !== null;
    }

    public function isPalmMute(): bool
    {
        return $this->palmMute;
    }

    public function isStaccato(): bool
    {
        return $this->staccato;
    }

    public function isLetRing(): bool
    {
        return $this->letRing;
    }

    public function isPopping(): bool
    {
        return $this->popping;
    }

    public function isSlapping(): bool
    {
        return $this->slapping;
    }

    public function isTapping(): bool
    {
        return $this->tapping;
    }

    public function isFadeIn(): bool
    {
        return $this->fadeIn;
    }

    public function hasAnyEffect(): bool
    {
        return $this->isBend()              ||
            $this->isTremoloBar()           ||
            $this->isHarmonic()             ||
            $this->isGrace()                ||
            $this->isTrill()                ||
            $this->isTremoloPicking()       ||
            $this->isVibrato()              ||
            $this->isDeadNote()             ||
            $this->isSlide()                ||
            $this->isHammer()               ||
            $this->isGhostNote()            ||
            $this->isAccentuatedNote()      ||
            $this->isHeavyAccentuatedNote() ||
            $this->isPalmMute()             ||
            $this->isLetRing()              ||
            $this->isStaccato()             ||
            $this->isTapping()              ||
            $this->isSlapping()             ||
            $this->isPopping()              ||
            $this->isFadeIn();
    }
}
