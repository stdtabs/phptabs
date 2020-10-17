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

class EffectPointsBase
{
    protected $position;
    protected $value;

    public function __construct(int $position, int $value)
    {
        $this->position = $position;
        $this->value = $value;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
