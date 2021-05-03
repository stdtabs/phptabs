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

namespace PhpTabs\Writer\Midi;

use PhpTabs\Music\Duration;
use PhpTabs\Music\Song;

final class MidiRepeatController
{
    /**
     * @var Song
     */
    private $song;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var int
     */
    private $lastIndex = -1;

    /**
     * @var bool
     */
    private $shouldPlay = true;

    /**
     * @var bool
     */
    private $repeatOpen = true;

    /**
     * @var int
     */
    private $repeatStart;

    /**
     * @var int
     */
    private $repeatEnd = 0;

    /**
     * @var int
     */
    private $repeatMove = 0;

    /**
     * @var int
     */
    private $repeatStartIndex = 0;

    /**
     * @var int
     */
    private $repeatNumber = 0;

    /**
     * @var int
     */
    private $repeatAlternative = 0;

    /**
     * @var int
     */
    private $sHeader;

    /**
     * @var int
     */
    private $eHeader;

    public function __construct(Song $song, int $sHeader, int $eHeader)
    {
        $this->song = $song;
        $this->sHeader = $sHeader;
        $this->eHeader = $eHeader;
        $this->count = $song->countMeasureHeaders();
        $this->repeatStart = Duration::QUARTER_TIME;
    }

    public function process(): void
    {
        $header = $this->song->getMeasureHeader($this->index);

        // Checks pointer is in range
        if (($this->sHeader !== -1 && $header->getNumber() < $this->sHeader) || ($this->eHeader !== -1 && $header->getNumber() > $this->eHeader)) {
            $this->shouldPlay = false;
            $this->index ++;
            return;
        }

        // always repeat open first
        if (($this->sHeader !== -1 && $header->getNumber() === $this->sHeader) || $header->getNumber() === 1) {
            $this->repeatStartIndex = $this->index;
            $this->repeatStart = $header->getStart();
            $this->repeatOpen = true;
        }

        // By default, should sound
        $this->shouldPlay = true;

        // If repeat open, memorize on which measure it starts
        if ($header->isRepeatOpen()) {
            $this->repeatStartIndex = $this->index;
            $this->repeatStart = $header->getStart();
            $this->repeatOpen = true;

            // First pass on the repeat
            if ($this->index > $this->lastIndex) {
                $this->repeatNumber = 0;
                $this->repeatAlternative = 0;
            }
        } else {
            // Checks if an alternative has been opened
            if ($this->repeatAlternative === 0) {
                $this->repeatAlternative = $header->getRepeatAlternative();
            }
            // Final alternative
            if ($this->repeatOpen && $this->repeatAlternative > 0 && ($this->repeatAlternative & (1 << $this->repeatNumber)) === 0) {
                $this->repeatMove -= $header->getLength();

                if ($header->getRepeatClose() > 0) {
                    $this->repeatAlternative = 0;
                }

                $this->shouldPlay = false;
                $this->index++;
                return;
            }
        }

        // Before executing a repeat, keep index of last one
        $this->lastIndex = max($this->lastIndex, $this->index);

        // If repeat, pass through
        if ($this->repeatOpen && $header->getRepeatClose() > 0) {
            if ($this->repeatNumber < $header->getRepeatClose() || ($this->repeatAlternative > 0)) {
                $this->repeatEnd = $header->getStart() + $header->getLength();
                $this->repeatMove += $this->repeatEnd - $this->repeatStart;
                $this->index = $this->repeatStartIndex - 1;
                $this->repeatNumber++;
            } else {
                $this->repeatStart = 0;
                $this->repeatNumber = 0;
                $this->repeatEnd = 0;
                $this->repeatOpen = false;
            }

            $this->repeatAlternative = 0;
        }

        $this->index++;
    }

    public function finished(): bool
    {
        return $this->index >= $this->count;
    }

    public function shouldPlay(): bool
    {
        return $this->shouldPlay;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getRepeatMove(): int
    {
        return $this->repeatMove;
    }
}
