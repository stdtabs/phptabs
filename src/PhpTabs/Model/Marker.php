<?php

namespace PhpTabs\Model;


class Marker
{
  public static $defaultColor = array(255, 0, 0);
  public static $defaultTitle = "Untitled";

  private $measure;
  private $title;
  private $color;

  public function __construct()
  {
    $this->measure = 0;
    $this->title = Marker::$defaultTitle;

    $color = new Color();
    $color->setR(Marker::$defaultColor[0]);
    $color->setG(Marker::$defaultColor[1]);
    $color->setB(Marker::$defaultColor[2]);
    $this->color = $color;
  }

  public function getMeasure()
  {
    return $this->measure;
  }

  public function setMeasure($measure)
  {
    $this->measure = $measure;
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    $this->title = (string)$title;
  }

  public function getColor()
  {
    return $this->color;
  }

  public function setColor(Color $color)
  {
    $this->color = $color;
  }

  public function __clone()
  {
    $marker = new Marker();
    $marker->copyFrom($this);
    return $marker;
  }

  public function copyFrom(Marker $marker)
  {
    $this->setMeasure($marker->getMeasure());
    $this->setTitle($marker->getTitle());
    $this->setColor($marker->getColor());
  }
}
