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
  /** @var array $red RGB red value */
  public static $red = array(255,0,0);
  
  /** @var array $green RGB green value */
  public static $green = array(0,255,0);
  
  /** @var array $blue RGB blue value */
  public static $blue = array(0,0,255);
  
  /** @var array $white RGB white value */
  public static $white = array(255,255,255);
  
  /** @var array $black RGB black value */
  public static $black = array(0,0,0);

  /** @var array of RGB code */
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
   * Clones current color
   *
   * @return \PhpTabs\Music\Color
   */
  public function __clone()
  {
    $color = new Color;
    $color->copyFrom($this);
    return $color;
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
