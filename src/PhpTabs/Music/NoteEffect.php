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

/**
 * @uses EffectBend
 * @uses EffectTremoloBar
 * @uses EffectHarmonic
 * @uses EffectGrace
 * @uses EffectTrill
 * @uses EffectTremoloPicking
 */
final class NoteEffect extends NoteEffectBase
{
    protected $bend           = null;
    protected $harmonic       = null;
    protected $grace          = null;
    protected $trill          = null;
    protected $tremoloBar     = null;
    protected $tremoloPicking = null;

    public function setDeadNote(bool $deadNote): void
    {
        $this->deadNote = $deadNote;

        if ($this->isDeadNote()) {
            $this->bend           = null;
            $this->trill          = null;
            $this->slide          = false;
            $this->hammer         = false;
            $this->tremoloBar     = null;
            $this->tremoloPicking = null;
        }
    }

    public function setVibrato(bool $vibrato): void
    {
        $this->vibrato = $vibrato;

        if ($this->isVibrato()) {
            $this->trill          = null;
            $this->tremoloPicking = null;
        }
    }

    public function getBend(): ?EffectBend
    {
        return $this->bend;
    }

    public function setBend(EffectBend $bend): void
    {
        $this->bend = $bend;

        if ($this->isBend()) {
            $this->trill          = null;
            $this->deadNote       = false;
            $this->slide          = false;
            $this->hammer         = false;
            $this->tremoloBar     = null;
            $this->tremoloPicking = null;
        }
    }

    public function getTremoloBar(): ?EffectTremoloBar
    {
        return $this->tremoloBar;
    }

    public function setTremoloBar(EffectTremoloBar $tremoloBar): void
    {
        $this->tremoloBar = $tremoloBar;

        if ($this->isTremoloBar()) {
            $this->bend           = null;
            $this->trill          = null;
            $this->deadNote       = false;
            $this->slide          = false;
            $this->hammer         = false;
            $this->tremoloPicking = null;
        }
    }

    public function getTrill(): ?EffectTrill
    {
        return $this->trill;
    }

    public function setTrill(EffectTrill $trill): void
    {
        $this->trill = $trill;

        if ($this->isTrill()) {
            $this->bend           = null;
            $this->tremoloBar     = null;
            $this->tremoloPicking = null;
            $this->slide          = false;
            $this->hammer         = false;
            $this->deadNote       = false;
            $this->vibrato        = false;
        }
    }

    public function getTremoloPicking(): ?EffectTremoloPicking
    {
        return $this->tremoloPicking;
    }

    public function setTremoloPicking(EffectTremoloPicking $tremoloPicking): void
    {
        $this->tremoloPicking = $tremoloPicking;

        if ($this->isTremoloPicking()) {
            $this->trill      = null;
            $this->bend       = null;
            $this->tremoloBar = null;
            $this->slide      = false;
            $this->hammer     = false;
            $this->deadNote   = false;
            $this->vibrato    = false;
        }
    }

    public function setHammer(bool $hammer): void
    {
        $this->hammer = $hammer;

        if ($this->isHammer()) {
            $this->trill          = null;
            $this->bend           = null;
            $this->deadNote       = false;
            $this->slide          = false;
            $this->tremoloBar     = null;
            $this->tremoloPicking = null;
        }
    }

    public function setSlide(bool $slide): void
    {
        $this->slide = $slide;

        if ($this->isSlide()) {
            $this->trill          = null;
            $this->bend           = null;
            $this->deadNote       = false;
            $this->hammer         = false;
            $this->tremoloBar     = null;
            $this->tremoloPicking = null;
        }
    }

    public function setGhostNote(bool $ghostNote): void
    {
        $this->ghostNote = $ghostNote;

        if ($this->isGhostNote()) {
            $this->accentuatedNote      = false;
            $this->heavyAccentuatedNote = false;
        }
    }

    public function setAccentuatedNote(bool $accentuatedNote): void
    {
        $this->accentuatedNote = $accentuatedNote;

        if ($this->isAccentuatedNote()) {
            $this->ghostNote            = false;
            $this->heavyAccentuatedNote = false;
        }
    }

    public function setHeavyAccentuatedNote(bool $heavyAccentuatedNote): void
    {
        $this->heavyAccentuatedNote = $heavyAccentuatedNote;

        if ($this->isHeavyAccentuatedNote()) {
            $this->ghostNote       = false;
            $this->accentuatedNote = false;
        }
    }

    public function setHarmonic(EffectHarmonic $harmonic): void
    {
        $this->harmonic = $harmonic;
    }

    public function getHarmonic(): ?EffectHarmonic
    {
        return $this->harmonic;
    }

    public function getGrace(): ?EffectGrace
    {
        return $this->grace;
    }

    public function setGrace(EffectGrace $grace): void
    {
        $this->grace = $grace;
    }

    public function setPalmMute(bool $palmMute): void
    {
        $this->palmMute = $palmMute;

        if ($this->isPalmMute()) {
            $this->staccato = false;
            $this->letRing  = false;
        }
    }

    public function setStaccato(bool $staccato): void
    {
        $this->staccato = $staccato;

        if ($this->isStaccato()) {
            $this->palmMute = false;
            $this->letRing  = false;
        }
    }

    public function setLetRing(bool $letRing): void
    {
        $this->letRing = $letRing;

        if ($this->isLetRing()) {
            $this->staccato = false;
            $this->palmMute = false;
        }
    }

    public function setPopping(bool $popping): void
    {
        $this->popping = $popping;

        if ($this->isPopping()) {
            $this->tapping  = false;
            $this->slapping = false;
        }
    }

    public function setSlapping(bool $slapping): void
    {
        $this->slapping = $slapping;

        if ($this->isSlapping()) {
            $this->tapping = false;
            $this->popping = false;
        }
    }

    public function setTapping(bool $tapping): void
    {
        $this->tapping = $tapping;

        if ($this->isTapping()) {
            $this->slapping = false;
            $this->popping  = false;
        }
    }

    public function setFadeIn(bool $fadeIn): void
    {
        $this->fadeIn = $fadeIn;
    }

    public function __clone()
    {
        if (!is_null($this->bend)) {
            $this->bend = clone $this->bend;
        }

        if (!is_null($this->harmonic)) {
            $this->harmonic = clone $this->harmonic;
        }

        if (!is_null($this->grace)) {
            $this->grace = clone $this->grace;
        }

        if (!is_null($this->trill)) {
            $this->trill = clone $this->trill;
        }

        if (!is_null($this->tremoloBar)) {
            $this->tremoloBar = clone $this->tremoloBar;
        }

        if (!is_null($this->tremoloPicking)) {
            $this->tremoloPicking = clone $this->tremoloPicking;
        }
    }
}
