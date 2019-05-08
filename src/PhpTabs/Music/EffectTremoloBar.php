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
 * @uses TremoloBarPoint
 */
class EffectTremoloBar
{
    const MAX_POSITION_LENGTH = 12;
    const MAX_VALUE_LENGTH    = 12;

    private $points = [];

    /**
     * @param int $position
     * @param int $value
     */
    public function addPoint($position, $value)
    {
        $this->points[] = new TremoloBarPoint($position, $value);
    }

    /**
     * @return array
     */
    public function getPoints()
    {
        return $this->points;
    }

    public function __clone()
    {
        $effect = new EffectTremoloBar();

        foreach ($this->points as $point) {
            $effect->addPoint($point->getPosition(), $point->getValue());
        }
    }
}

/**
 * @uses EffectTremoloBar
 */
class TremoloBarPoint extends EffectPointsBase
{
    /**
     * @param  int $duration
     * @return int
     */
    public function getTime($duration)
    {
        return intval(
            $duration * $this->getPosition()
            / EffectTremoloBar::MAX_POSITION_LENGTH
        );
    }
}
