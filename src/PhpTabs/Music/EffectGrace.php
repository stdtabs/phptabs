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

/**
 * @uses Velocities
 * @uses Duration
 */
final class EffectGrace
{
    public const TRANSITION_NONE   = 0;
    public const TRANSITION_SLIDE  = 1;
    public const TRANSITION_BEND   = 2;
    public const TRANSITION_HAMMER = 3;

    private $fret     = 0;
    private $duration = 1;
    private $onBeat   = false;
    private $dead     = false;
    private $dynamic;
    private $transition;

    public function __construct()
    {
        $this->dynamic    = Velocities::_DEFAULT;
        $this->transition = EffectGrace::TRANSITION_NONE;
    }

    public function isDead(): bool
    {
        return $this->dead;
    }

    public function setDead(bool $dead): void
    {
        $this->dead = $dead;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getDynamic(): int
    {
        return $this->dynamic;
    }

    public function setDynamic(int $dynamic): void
    {
        $this->dynamic = $dynamic;
    }

    public function getFret(): int
    {
        return $this->fret;
    }

    public function setFret(int $fret): void
    {
        $this->fret = $fret;
    }

    public function isOnBeat(): bool
    {
        return $this->onBeat;
    }

    public function setOnBeat(bool $onBeat): void
    {
        $this->onBeat = $onBeat;
    }

    public function getTransition(): int
    {
        return $this->transition;
    }

    public function setTransition(int $transition): void
    {
        $this->transition = $transition;
    }

    public function getDurationTime(): int
    {
        return intval(
            Duration::QUARTER_TIME / 16.00  * $this->getDuration()
        );
    }
}
