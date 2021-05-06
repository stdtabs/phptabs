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

final class Track
{
    public const MAX_OFFSET = 24;
    public const MIN_OFFSET = -24;

    /**
     * @var int
     */
    private $number = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var int
     */
    private $channelId = -1;

    /**
     * @var bool
     */
    private $solo = false;

    /**
     * @var bool
     */
    private $mute = false;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array<Measure>
     */
    private $measures = [];

    /**
     * @var array<TabString>
     */
    private $strings = [];

    /**
     * @var ?Color
     */
    private $color;

    /**
     * @var ?Lyric
     */
    private $lyrics;

    /**
     * @var Song
     */
    private $song;

    public function __construct()
    {
        $this->color = new Color();
        $this->lyrics = new Lyric();
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return array<Measure>
     */
    public function getMeasures(): array
    {
        return $this->measures;
    }

    public function addMeasure(Measure $measure): void
    {
        $measure->setTrack($this);
        $this->measures[] = $measure;
    }

    public function getMeasure(int $index): ?Measure
    {
        return $this->measures[$index] ?? null;
    }

    /**
     * Remove a measure by measure number
     */
    public function removeMeasure(int $number): void
    {
        $this->measures = array_filter(
            $this->measures,
            static function ($item) use ($number) {
                return $item->getNumber() !== $number;
            }
        );

        $this->measures = array_values($this->measures);
    }

    public function countMeasures(): int
    {
        return count($this->measures);
    }

    /**
     * @return array<TabString>
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    public function addString(TabString $string): void
    {
        $this->strings[] = $string;
    }

    /**
     * @param array<TabString> $strings
     */
    public function setStrings(array $strings): void
    {
        foreach ($strings as $string) {
            $this->addString($string);
        }
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): void
    {
        $this->color = $color;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function isSolo(): bool
    {
        return $this->solo;
    }

    public function setSolo(bool $solo): void
    {
        $this->solo = $solo;
    }

    public function isMute(): bool
    {
        return $this->mute;
    }

    public function setMute(bool $mute): void
    {
        $this->mute = $mute;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function getLyrics(): Lyric
    {
        return $this->lyrics;
    }

    public function setLyrics(Lyric $lyrics): void
    {
        $this->lyrics = $lyrics;
    }

    public function getString(int $number): TabString
    {
        return $this->strings[$number - 1];
    }

    public function countStrings(): int
    {
        return count($this->strings);
    }

    public function getSong(): Song
    {
        return $this->song;
    }

    public function setSong(Song $song): void
    {
        $this->song = $song;
    }

    public function clear(): void
    {
        $measureCount = $this->countMeasures();

        for ($i = 0; $i < $measureCount; $i++) {
            $measure = $this->getMeasure($i);
            $measure->clear();
        }

        $this->strings  = [];
        $this->measures = [];
    }

    public function __clone()
    {
        if (!is_null($this->color)) {
            $this->color = clone $this->color;
        }

        if (!is_null($this->lyrics)) {
            $this->lyrics = clone $this->lyrics;
        }

        foreach ($this->strings as $index => $item) {
            $this->strings[$index] = clone $item;
        }

        foreach ($this->measures as $index => $item) {
            $this->measures[$index] = clone $item;
        }
    }

    public function copyFrom(Track $track): void
    {
        $this->clear();
        $this->setNumber($track->getNumber());
        $this->setName($track->getName());
        $this->setOffset($track->getOffset());
        $this->setSolo($track->isSolo());
        $this->setMute($track->isMute());
        $this->setChannelId($track->getChannelId());
        $this->getColor()->copyFrom(clone $track->getColor());
        $this->getLyrics()->copyFrom(clone $track->getLyrics());

        $count = $track->countStrings();
        for ($i = 0; $i < $count; $i++) {
            $this->strings[$i] = clone $track->getString($i + 1);
        }

        $count = $track->countMeasures();
        for ($i = 0; $i < $count; $i++) {
            $measure = clone $track->getMeasure($i);
            $this->addMeasure(clone $measure);
        }
    }
}
