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

class Chord
{
    /**
     * @var int $firstFret first fret id
     */
    private $firstFret = 0;

    /**
     * @var array $strings list of strings ids
     */
    private $strings = [];

    /**
     * @var string $name of the chord
     */
    private $name;

    /**
     * @var \PhpTabs\Music\Beat $beat
     */
    private $beat;

    /**
     * @param int $length Number of strings
     */
    public function __construct(int $length)
    {
        $this->strings = array_fill(0, $length, -1);
    }

    public function getBeat(): Beat
    {
        return $this->beat;
    }

    public function setBeat(Beat $beat): void
    {
        $this->beat = $beat;
    }

    /**
     * Puts a fret value
     */
    public function addFretValue(int $string, int $fret): void
    {
        if ($string >= 0 && $string < count($this->strings)) {
            $this->strings[$string] = $fret;
        }
    }

    /**
     * Gets a fret value by string index
     */
    public function getFretValue(int $string): int
    {
        if ($string >= 0 && $string < count($this->strings)) {
            return $this->strings[$string];
        }

        return -1;
    }

    /**
     * Gets the first fret id
     */
    public function getFirstFret(): int
    {
        return $this->firstFret;
    }

    /**
     * Sets the first fret id
     */
    public function setFirstFret(int $firstFret): void
    {
        $this->firstFret = $firstFret;
    }

    /**
     * Gets list of strings ids
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    /**
     * Gets number of strings
     */
    public function countStrings(): int
    {
        return count($this->strings);
    }

    /**
     * Gets number of notes which compounds the chord
     */
    public function countNotes(): int
    {
        return count(
            array_filter(
                $this->strings,
                function ($value) {
                    return $value >= 0;
                }
            )
        );
    }

    /**
     * Gets the chord name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the chord name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
