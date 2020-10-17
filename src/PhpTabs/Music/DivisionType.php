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
 * Modelizes division between 2 notes
 */
class DivisionType
{
    private $enters = 1;
    private $times  = 1;

    public function getEnters(): int
    {
        return $this->enters;
    }

    public function setEnters(int $enters): void
    {
        $this->enters = $enters;
    }

    public function getTimes(): int
    {
        return $this->times;
    }

    public function setTimes(int $times): void
    {
        $this->times = $times;
    }

    public function convertTime(int $time): int
    {
        return intval($time * $this->times / $this->enters);
    }

    public function isEqual(DivisionType $divisionType): bool
    {
        return $divisionType->getEnters() == $this->getEnters()
            && $divisionType->getTimes()  == $this->getTimes();
    }

    public function copyFrom(DivisionType $divisionType): void
    {
        $this->setEnters($divisionType->getEnters());
        $this->setTimes($divisionType->getTimes());
    }

    public static function normal(): DivisionType
    {
        return self::newDivisionType(1, 1);
    }

    public static function triplet(): DivisionType
    {
        return self::newDivisionType(3, 2);
    }

    public static function alteredDivisionTypes(): array
    {
        return [
            self::newDivisionType(3, 2),
            self::newDivisionType(5, 4),
            self::newDivisionType(6, 4),
            self::newDivisionType(7, 4),
            self::newDivisionType(9, 8),
            self::newDivisionType(10, 8),
            self::newDivisionType(11, 8),
            self::newDivisionType(12, 8),
            self::newDivisionType(13, 8),
        ];
    }

    private static function newDivisionType(int $enters, int $times): DivisionType
    {
        $divisionType = new DivisionType();
        $divisionType->setEnters($enters);
        $divisionType->setTimes($times);
        return $divisionType;
    }
}
