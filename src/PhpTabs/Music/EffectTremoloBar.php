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
 * @uses TremoloBarPoint
 */
final class EffectTremoloBar
{
    public const MAX_POSITION_LENGTH = 12;
    public const MAX_VALUE_LENGTH    = 12;

    private $points = [];

    public function addPoint(int $position, int $value): void
    {
        $this->points[] = new EffectTremoloBarPoint($position, $value);
    }

    /**
     * @return array<EffectTremoloBarPoint>
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function __clone()
    {
        foreach ($this->points as $index => $item) {
            $this->points[$index] = clone $item;
        }
    }
}
