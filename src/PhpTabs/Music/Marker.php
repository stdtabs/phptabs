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

class Marker
{
    public static $defaultColor = [255, 0, 0];
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

    public function getMeasure(): int
    {
        return $this->measure;
    }

    public function setMeasure(int $measure): void
    {
        $this->measure = $measure;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): void
    {
        $this->color = $color;
    }

    public function copyFrom(Marker $marker): void
    {
        $this->setMeasure($marker->getMeasure());
        $this->setTitle($marker->getTitle());
        $this->setColor($marker->getColor());
    }
}
