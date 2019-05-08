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

    /**
     * @return int
     */
    public function getEnters()
    {
        return $this->enters;
    }

    /**
     * @param int $enters
     */
    public function setEnters($enters)
    {
        $this->enters = $enters;
    }

    /**
     * @return int
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @param int $times
     */
    public function setTimes($times)
    {
        $this->times = $times;
    }

    /**
     * @return int
     */
    public function convertTime($time)
    {
        return intval($time * $this->times / $this->enters);
    }

    /**
     * @param  \PhpTabs\Music\DivisionType $divisionType
     * @return bool
     */
    public function isEqual(DivisionType $divisionType)
    {
        return $divisionType->getEnters() == $this->getEnters() 
            && $divisionType->getTimes()  == $this->getTimes();
    }

    /**
     * @param \PhpTabs\Music\DivisionType $divisionType
     */
    public function copyFrom(DivisionType $divisionType)
    {
        $this->setEnters($divisionType->getEnters());
        $this->setTimes($divisionType->getTimes());
    }

    /**
     * @return \PhpTabs\Music\DivisionType
     */
    public static function normal()
    {
        return self::newDivisionType(1, 1);
    }

    /**
     * @return \PhpTabs\Music\DivisionType
     */
    public static function triplet()
    {
        return self::newDivisionType(3, 2);
    }

    /**
     * @return array
     */
    public static function alteredDivisionTypes()
    {
        return array(
            self::newDivisionType(3, 2),
            self::newDivisionType(5, 4),
            self::newDivisionType(6, 4),
            self::newDivisionType(7, 4),
            self::newDivisionType(9, 8),
            self::newDivisionType(10, 8),
            self::newDivisionType(11, 8),
            self::newDivisionType(12, 8),
            self::newDivisionType(13, 8),
        );
    }

    /**
     * @return \PhpTabs\Music\DivisionType
     */
    private static function newDivisionType(int $enters, int $times)
    {
        $divisionType = new DivisionType();
        $divisionType->setEnters($enters);
        $divisionType->setTimes($times);
        return $divisionType;
    }

}
