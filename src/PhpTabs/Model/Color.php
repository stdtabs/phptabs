<?php

namespace PhpTabs\Model;

/**
 * @package Color
 */
 
class Color
{
  public static $RED = array(255,0,0);
  public static $GREEN = array(0,255,0);
  public static $BLUE = array(0,0,255);
  public static $WHITE = array(255,255,255);
  public static $BLACK = array(0,0,0);

  private $value = array();


  public function __construct()
  {
    $this->value = Color::$BLACK;
  }

  public function getB()
  {
    return $this->value[2];
  }

  public function setB($b)
  {
    $this->value[2] = $b;
  }

  public function getG()
  {
    return $this->value[1];
  }

  public function setG($g)
  {
    $this->value[1] = $g;
  }

  public function getR()
  {
    return $this->value[0];
  }

  public function setR($r)
  {
    $this->value[0] = $r;
  }

  public function isEqual(Color $color)
  {
    return ($this->getR() == $color->getR() 
      && $this->getG() == $color->getG()
      && $this->getB() == $color->getB());
  }

  public function __clone()
  {
    $color = new Color;
    $color->copyFrom($this);
    return $color;
  }

  public function copyFrom(Color $color)
  {
    $this->setR($color->getR());
    $this->setG($color->getG());
    $this->setB($color->getB());
  }

  public static function toArray($r, $g, $b)
  {
    $color = new Color();
    $color->setR($r);
    $color->setG($g);
    $color->setB($b);
    return $color->value;
  }
}
