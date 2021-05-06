<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

final class Marker
{
    public const DEFAULT_COLOR = [255, 0, 0];
    public const DEFAULT_TITLE = "Untitled";

    /**
     * @var int
     */
    private $measure = 0;

    /**
     * @var string
     */
    private $title = Marker::DEFAULT_TITLE;

    /**
     * @var Color
     */
    private $color;

    public function __construct()
    {
        $color = new Color();
        $color->setR(Marker::DEFAULT_COLOR[0]);
        $color->setG(Marker::DEFAULT_COLOR[1]);
        $color->setB(Marker::DEFAULT_COLOR[2]);
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
