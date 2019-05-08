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
 * @uses \PhpTabs\Music\Duration
 */
class EffectTremoloPicking
{
    private $duration;

    public function __construct()
    {
        $this->duration = new Duration();
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
