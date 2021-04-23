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
final class TimeSignature
{
    /**
     * @var int
     */
    private $numerator = 4;

    /**
     * @var Duration
     */
    private $denominator;

    public function __construct()
    {
        $this->denominator = new Duration();
    }

    public function getNumerator(): int
    {
        return $this->numerator;
    }

    public function setNumerator(int $numerator): void
    {
        $this->numerator = $numerator;
    }

    public function getDenominator(): Duration
    {
        return $this->denominator;
    }

    public function setDenominator(Duration $denominator): void
    {
        $this->denominator = $denominator;
    }

    public function copyFrom(TimeSignature $timeSignature): void
    {
        $this->setNumerator($timeSignature->getNumerator());
        $this->getDenominator()->copyFrom($timeSignature->getDenominator());
    }

    public function isEqual(TimeSignature $timeSignature): bool
    {
        return $this->getNumerator() === $timeSignature->getNumerator()
            && $this->getDenominator()->isEqual(
                $timeSignature->getDenominator()
            );
    }
}
