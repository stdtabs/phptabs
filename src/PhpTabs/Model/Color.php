<?php

namespace PhpTabs\Model;

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
   * @return void
   */
  public function __construct()
  {
    $this->value = Color::$black;
  }

  /**
   * Gets RGB blue value
   * @return integer index of blue
   */
  public function getB()
  {
    return $this->value[2];
  }

  /**
   * Sets RGB blue value
   * @param integer $b blue index
   * @return void
   */
  public function setB($b)
  {
    $this->value[2] = $b;
  }

  /**
   * Gets RGB green value
   * @return integer index of green
   */
  public function getG()
  {
    return $this->value[1];
  }

  /**
   * Sets RGB green value
   * @param integer $b green index 
   * @return void
   */
  public function setG($g)
  {
    $this->value[1] = $g;
  }

  /**
   * Gets RGB red value
   * @return integer index of red
   */
  public function getR()
  {
    return $this->value[0];
  }

  /**
   * Sets RGB red value
   * @param integer $b red index 
   * @return void
   */
  public function setR($r)
  {
    $this->value[0] = $r;
  }

  /**
   * Compares two colors
   * @param Chord $color the color to compare with current color
   * @return boolean Result of the comparison
   */
  public function isEqual(Color $color)
  {
    return ($this->getR() == $color->getR() 
      && $this->getG() == $color->getG()
      && $this->getB() == $color->getB());
  }

  /**
   * Clones current color
   * @return Color
   */
  public function __clone()
  {
    $color = new Color;
    $color->copyFrom($this);
    return $color;
  }

  /**
   * Copies a color from anoterh one
   * @param Color $color
   * @return void
   */
  public function copyFrom(Color $color)
  {
    $this->setR($color->getR());
    $this->setG($color->getG());
    $this->setB($color->getB());
  }

  /**
   * Transforms a list of RGB codes into an array
   * @param integer $r red value
   * @param integer $g green value
   * @param integer $b blue value
   * @return array RGB array
   */
  public static function toArray($r, $g, $b)
  {
    $color = new Color();
    $color->setR($r);
    $color->setG($g);
    $color->setB($b);

    return $color->value;
  }
}
