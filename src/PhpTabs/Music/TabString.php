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

final class TabString
{
    /**
     * String number
     *
     * @var int $number
     */
    private $number;

    /**
     * String value
     *
     * @var int $value
     */
    private $value;

    public function __construct(int $number = 0, int $value = 0)
    {
        $this->number = $number;
        $this->value  = $value;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * Compare two strings
     */
    public function isEqual(TabString $string): bool
    {
        return $this->getNumber() === $string->getNumber()
            && $this->getValue()  === $string->getValue();
    }

    /**
     * Copy a string from another one
     */
    public function copyFrom(TabString $string): void
    {
        $this->setNumber($string->getNumber());
        $this->setValue($string->getValue());
    }
}
