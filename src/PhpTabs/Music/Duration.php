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

class Duration
{
    const QUARTER_TIME = 960;
    const WHOLE = 1;
    const HALF = 2;
    const QUARTER = 4;
    const EIGHTH = 8;
    const SIXTEENTH = 16;
    const THIRTY_SECOND = 32;
    const SIXTY_FOURTH = 64;

    private $value;
    private $divisionType;
    private $dotted       = false;
    private $doubleDotted = false;

    public function __construct()
    {
        $this->value        = Duration::QUARTER;
        $this->divisionType = new DivisionType();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function isDotted(): bool
    {
        return $this->dotted;
    }

    public function setDotted(bool $dotted): void
    {
        $this->dotted = (boolean)$dotted;
    }

    public function isDoubleDotted(): bool
    {
        return (boolean)$this->doubleDotted;
    }

    public function setDoubleDotted(bool $doubleDotted): void
    {
        $this->doubleDotted = (boolean)$doubleDotted;
    }

    public function getDivision(): DivisionType
    {
        return $this->divisionType;
    }

    public function getTime(): int
    {
        $time = Duration::QUARTER_TIME * (4.0 / $this->value);

        if ($this->dotted) {
            $time += $time / 2;
        } elseif ($this->doubleDotted) {
            $time += ($time / 4) * 3;
        }

        return $this->getDivision()->convertTime($time);
    }

    /**
     * Get a Duration object from time
     */
    public static function fromTime(int $time, Duration $minDuration = null, int $diff = null): Duration
    {
        if (is_null($minDuration)) {
            $duration = new Duration();
            $duration->setValue(self::SIXTY_FOURTH);
            $duration->setDotted(false);
            $duration->setDoubleDotted(false);
            $duration->getDivision()->setEnters(3);
            $duration->getDivision()->setTimes(2);

            return self::fromTime($time, $duration);
        } elseif (is_null($diff)) {
            return self::fromTime($time, $minDuration, 10);
        }

        $duration = clone $minDuration;
        $tmpDuration = new Duration();
        $tmpDuration->setValue(self::WHOLE);
        $tmpDuration->setDotted(true);
        $finish = false;

        while (!$finish) {
            $tmpTime = $tmpDuration->getTime();
            if ($tmpTime - $diff <= $time) {
                if (abs($tmpTime - $time) < abs($duration->getTime() - $time)) {
                    $duration = clone $tmpDuration;
                }
            }

            if ($tmpDuration->isDotted()) {
                $tmpDuration->setDotted(false);
            } elseif ($tmpDuration->getDivision()->isEqual(DivisionType::normal())) {
                $tmpDuration->getDivision()->setEnters(3);
                $tmpDuration->getDivision()->setTimes(2);
            } else {
                $tmpDuration->setValue($tmpDuration->getValue() * 2);
                $tmpDuration->setDotted(true);
                $tmpDuration->getDivision()->setEnters(1);
                $tmpDuration->getDivision()->setTimes(1);
            }

            if ($tmpDuration->getValue() > self::SIXTY_FOURTH) {
                $finish = true;
            }
        }

        return $duration;
    }

    public function getIndex(): int
    {
        $index = 0;
        $value = $this->value;
        while (($value = ($value >> 1) ) > 0) {
            $index++;
        }

        return $index;
    }

    public function isEqual(Duration $duration): bool
    {
        return $this->getValue() == $duration->getValue()
            && $this->isDotted() == $duration->isDotted()
            && $this->isDoubleDotted() == $duration->isDoubleDotted()
            && $this->getDivision()->isEqual($duration->getDivision());
    }

    /**
     * @return void
     * @todo fix this clone method that makes bugs into MidiReader
     */
    public function __clone()
    {
        // $this->divisionType = clone $this->divisionType;
    }

    public function copyFrom(Duration $duration): void
    {
        $this->setValue($duration->getValue());
        $this->setDotted($duration->isDotted());
        $this->setDoubleDotted($duration->isDoubleDotted());
        $this->getDivision()->copyFrom($duration->getDivision());
    }
}
