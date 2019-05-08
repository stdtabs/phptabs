<?php

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
class EffectTrill
{
    private $fret = 0;
    private $duration;

    public function __construct()
    {
        $this->duration = new Duration();
    }

    /**
     * @return int
     */
    public function getFret()
    {
        return $this->fret;
    }

    /**
     * @param int $fret
     */
    public function setFret($fret)
    {
        $this->fret = $fret;
    }

    /**
     * @return \PhpTabs\Music\Duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param \PhpTabs\Music\Duration $duration
     */
    public function setDuration(Duration $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->duration = clone $this->duration;
    }
}
