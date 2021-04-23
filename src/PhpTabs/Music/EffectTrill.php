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
 * @uses Duration
 */
final class EffectTrill
{
    private $fret = 0;
    private $duration;

    public function __construct()
    {
        $this->duration = new Duration();
    }

    public function getFret(): int
    {
        return $this->fret;
    }

    public function setFret(int $fret): void
    {
        $this->fret = $fret;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
    }

    public function __clone()
    {
        $this->duration = clone $this->duration;
    }
}
