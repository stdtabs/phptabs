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

class Lyric
{
    const REGEX = " ";

    private $from;
    private $lyrics;

    public function __construct()
    {
        $this->from   = 1;
        $this->lyrics = [];
    }

    public function getFrom(): int
    {
        return $this->from;
    }

    public function setFrom(int $from): void
    {
        $this->from = $from;
    }

    public function getLyrics(): array
    {
        return $this->lyrics;
    }

    /**
     * @param array|string $lyrics
     * @todo Fix given type either string or array
     */
    public function setLyrics($lyrics): void
    {
        $this->lyrics = $lyrics;
    }

    public function getLyricBeats(): array
    {
        $lyrics = $this->getLyrics();

        $str = '';

        foreach ($lyrics as $value) {
            $str .= str_replace(array("\n", "\r"), Lyric::REGEX, $value) . Lyric::REGEX;
        }

        return explode(Lyric::REGEX, $str);
    }

    public function isEmpty(): bool
    {
        return count($this->getLyrics()) == 0;
    }

    public function copyFrom(Lyric $lyric): void
    {
        $this->setFrom($lyric->getFrom());
        $this->setLyrics($lyric->getLyrics());
    }
}
