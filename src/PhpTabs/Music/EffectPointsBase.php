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

abstract class EffectPointsBase
{
    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
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
