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

final class Lyric
{
    const REGEX = " ";

    /**
     * @var int
     */
    private $from = 1;

    /**
     * @var string
     */
    private $lyrics = '';

    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int $from): void
    {
        $this->from = $from;
    }

    public function getLyrics(): string
    {
        return $this->lyrics;
    }

    public function setLyrics(string $lyrics): void
    {
        $this->lyrics = $lyrics;
    }

    public function getLyricBeats(): array
    {
        $lyrics = $this->getLyrics();

        $str = str_replace(["\n", "\r"], Lyric::REGEX, $lyrics) . Lyric::REGEX;

        return explode(Lyric::REGEX, $str);
    }

    public function isEmpty(): bool
    {
        return strlen($this->getLyrics()) === 0;
    }

    public function copyFrom(Lyric $lyric): void
    {
        $this->setFrom($lyric->getFrom());
        $this->setLyrics($lyric->getLyrics());
    }
}
