<?php

declare(strict_types = 1);

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
    protected $vibrato              = false;
    protected $deadNote             = false;
    protected $slide                = false;
    protected $hammer               = false;
    protected $ghostNote            = false;
    protected $palmMute             = false;
    protected $staccato             = false;
    protected $tapping              = false;
    protected $slapping             = false;
    protected $popping              = false;
    protected $fadeIn               = false;
    protected $letRing              = false;
    protected $accentuatedNote      = false;
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
        return
            $this->isBend()                 ||
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
