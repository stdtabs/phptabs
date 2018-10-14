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

  /**
   * @return int
   */
  public function getMeasure()
  {
    return $this->measure;
  }

  /**
   * @param int $measure
   */
  public function setMeasure($measure)
  {
    $this->measure = $measure;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * @return \PhpTabs\Music\Color
   */
  public function getColor()
  {
    return $this->color;
  }

  /**
   * @param \PhpTabs\Music\Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }

  /**
   * @return \PhpTabs\Music\Marker
   */
  public function __clone()
  {
    $marker = new Marker();
    $marker->copyFrom($this);
    return $marker;
  }

  /**
   * @param \PhpTabs\Music\Marker $marker
   */
  public function copyFrom(Marker $marker)
  {
    $this->setMeasure($marker->getMeasure());
    $this->setTitle($marker->getTitle());
    $this->setColor($marker->getColor());
  }
}
