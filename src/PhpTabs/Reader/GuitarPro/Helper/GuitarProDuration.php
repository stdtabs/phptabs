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

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Duration;

class GuitarProDuration extends AbstractReader
{
    /**
     * Read Duration from unsigned bytes
     */
    public function readDuration(int $flags): Duration
    {
        $duration = new Duration();

        $value = $this->reader->readByte();

        // @todo Fix this
        // Sometimes, it overrides PHP_INT_SIZE resulting to 0
        // when casting to int
        $tempValue = pow(2, ($value + 4)) / 4;
        $duration->setValue(intval($tempValue));
        $duration->setDotted(($flags & 0x01) != 0);

        if (($flags & 0x20) != 0) {
            $divisionType = $this->reader->readInt();

            switch ($divisionType) {
                case 3:
                    $duration->getDivision()->setEnters(3);
                    $duration->getDivision()->setTimes(2);
                    break;
                case 5:
                    $duration->getDivision()->setEnters(5);
                    $duration->getDivision()->setTimes(4);
                    break;
                case 6:
                    $duration->getDivision()->setEnters(6);
                    $duration->getDivision()->setTimes(4);
                    break;
                case 7:
                    $duration->getDivision()->setEnters(7);
                    $duration->getDivision()->setTimes(4);
                    break;
                case 9:
                    $duration->getDivision()->setEnters(9);
                    $duration->getDivision()->setTimes(8);
                    break;
                case 10:
                    $duration->getDivision()->setEnters(10);
                    $duration->getDivision()->setTimes(8);
                    break;
                case 11:
                    $duration->getDivision()->setEnters(11);
                    $duration->getDivision()->setTimes(8);
                    break;
                case 12:
                    $duration->getDivision()->setEnters(12);
                    $duration->getDivision()->setTimes(8);
                    break;
            }
        }

        return $duration;
    }
}
