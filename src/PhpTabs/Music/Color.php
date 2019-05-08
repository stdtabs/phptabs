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
     *
     * @return int Blue index
     */
    public function getB()
    {
        return $this->value[2];
    }

    /**
     * Sets RGB blue value
     *
     * @param int $blue Blue index
     */
    public function setB($blue)
    {
        $this->value[2] = $blue;
    }

    /**
     * Gets RGB green value
     *
     * @return int Green index
     */
    public function getG()
    {
        return $this->value[1];
    }

    /**
     * Sets RGB green value
     * 
     * @param int $green Green index 
     */
    public function setG($green)
    {
        $this->value[1] = $green;
    }

    /**
     * Gets RGB red value
     *
     * @return int Red index
     */
    public function getR()
    {
        return $this->value[0];
    }

    /**
     * Sets RGB red value
     *
     * @param int $red Red index 
     */
    public function setR($red)
    {
        $this->value[0] = $red;
    }

    /**
     * Compares two colors
     *
     * @param \PhpTabs\Music\Color $color A color to compare with current color
     * 
     * @return bool Result of the comparison
     */
    public function isEqual(Color $color)
    {
        return $this->getR() == $color->getR() 
        && $this->getG() == $color->getG()
        && $this->getB() == $color->getB();
    }

    /**
     * Copies a color from another one
     * 
     * @param \PhpTabs\Music\Color $color
     */
    public function copyFrom(Color $color)
    {
        $this->setR($color->getR());
        $this->setG($color->getG());
        $this->setB($color->getB());
    }

    /**
     * Transforms a list of RGB codes into an array
     *
     * @param int $red   red value
     * @param int $green green value
     * @param int $blue  blue value
     *
     * @return array RGB array
     */
    public static function toArray($red, $green, $blue)
    {
        $color = new Color();
        $color->setR($red);
        $color->setG($green);
        $color->setB($blue);

        return $color->value;
    }
}
