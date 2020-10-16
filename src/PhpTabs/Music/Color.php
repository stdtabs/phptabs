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
 * Color styling of tracks & markers
 * RGB
 */
class Color
{
    /**
     * RGB red value
     *
     * @var array $red
     */
    public static $red = array(255,0,0);

    /**
     *  RGB green value
     *
     * @var array $green
     */
    public static $green = array(0,255,0);

    /**
     * RGB blue value
     *
     * @var array $blue
     */
    public static $blue = array(0,0,255);

    /**
     * RGB white value
     *
     * @var array $white
     */
    public static $white = array(255,255,255);

    /**
     * RGB black value
     *
     * @var array $black
     */
    public static $black = array(0,0,0);

    /**
     * @var array
     */
    private $value = array();

    /**
     * Constructor sets black color by default
     */
    public function __construct()
    {
        $this->value = Color::$black;
    }

    /**
     * Gets RGB blue value
     */
    public function getB(): int
    {
        return $this->value[2];
    }

    /**
     * Sets RGB blue value
     */
    public function setB(int $blue): void
    {
        $this->value[2] = $blue;
    }

    /**
     * Gets RGB green value
     */
    public function getG(): int
    {
        return $this->value[1];
    }

    /**
     * Sets RGB green value
     */
    public function setG(int $green): void
    {
        $this->value[1] = $green;
    }

    /**
     * Gets RGB red value
     */
    public function getR(): int
    {
        return $this->value[0];
    }

    /**
     * Sets RGB red value
     */
    public function setR(int $red): void
    {
        $this->value[0] = $red;
    }

    /**
     * Compares two colors
     */
    public function isEqual(Color $color): bool
    {
        return $this->getR() == $color->getR()
            && $this->getG() == $color->getG()
            && $this->getB() == $color->getB();
    }

    /**
     * Copies a color from another one
     */
    public function copyFrom(Color $color): void
    {
        $this->setR($color->getR());
        $this->setG($color->getG());
        $this->setB($color->getB());
    }

    /**
     * Transforms a list of RGB codes into an array
     */
    public static function toArray(int $red, int $green, int $blue): array
    {
        $color = new Color();
        $color->setR($red);
        $color->setG($green);
        $color->setB($blue);

        return $color->value;
    }
}
