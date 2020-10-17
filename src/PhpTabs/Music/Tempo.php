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
 * Tempo representations with some helpers
 */
class Tempo
{
    /**
     * @const SECOND_IN_MILLIS
     */
    const SECOND_IN_MILLIS = 1000;

    /**
     * Current value of the tempo
     *
     * @var int $value
     */
    private $value = 120;

    /**
     * Gets tempo value
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Sets tempo value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * Gets a tick in millisecond
     */
    public function getInMillis(): int
    {
        return intval(60 / $this->getValue() * Tempo::SECOND_IN_MILLIS);
    }

    /**
     * Gets a tick in time per quarter
     */
    public function getInTPQ(): int
    {
        return intval((60 / $this->getValue() * Tempo::SECOND_IN_MILLIS) * 1000);
    }

    /**
     * Creates a tempo from TPQ
     */
    public static function fromTPQ(int $tpq): Tempo
    {
        $value = intval((60 * Tempo::SECOND_IN_MILLIS) / ($tpq / 1000));
        $tempo = new Tempo();
        $tempo->setValue($value);
        return $tempo;
    }

    /**
     * Copies a tempo from another one
     */
    public function copyFrom(Tempo $tempo): void
    {
        $this->setValue($tempo->getValue());
    }
}
