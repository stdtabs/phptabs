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

/**
 * @uses Beat
 * @uses Duration
 * @uses Note
 */
final class Voice
{
    public const DIRECTION_NONE = 0;
    public const DIRECTION_UP   = 1;
    public const DIRECTION_DOWN = 2;

    /**
     * @var Beat
     */
    private $beat;

    /**
     * @var Duration
     */
    private $duration;

    /**
     * @var int
     */
    private $index;
    
    /**
     * @var int
     */
    private $direction;

    /**
     * @var array<Note>
     */
    private $notes = [];

    /**
     * @var bool
     */
    private $empty = true;

    public function __construct(int $index)
    {
        $this->duration  = new Duration();
        $this->index     = $index;
        $this->direction = Voice::DIRECTION_NONE;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    public function isEmpty(): bool
    {
        return $this->empty;
    }

    public function setEmpty(bool $empty): void
    {
        $this->empty = $empty;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function setDirection(int $direction): void
    {
        $this->direction = $direction;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function setDuration(Duration $duration): void
    {
        $this->duration = $duration;
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
     * @return array<Note>
     */
    public function getNotes(): array
    {
        return $this->notes;
    }

    public function addNote(Note $note): void
    {
        $note->setVoice($this);
        $this->notes[] = $note;
        $this->setEmpty(false);
    }

    public function moveNote(int $index, Note $note): void
    {
        $this->removeNote($note);

        array_splice($this->notes, $index, 0, [$note]);
    }

    public function removeNote(Note $note): void
    {
        foreach ($this->notes as $k => $v) {
            if ($v === $note) {
                array_splice($this->notes, $k, 1);

                if (!$this->countNotes()) {
                    $this->setEmpty(true);
                }

                return;
            }
        }
    }

    public function getNote(int $index): ?Note
    {
        return $this->notes[$index] ?? null;
    }

    public function countNotes(): int
    {
        return count($this->notes);
    }

    public function isRestVoice(): bool
    {
        return $this->countNotes() === 0;
    }

    /**
     * Get duration in seconds
     */
    public function getTime(): float
    {
        $measure = $this->getBeat()->getMeasure();

        $time = 60
            * $measure->getTimeSignature()->getNumerator()
            / $measure->getTempo()->getValue();

        return $time
             * $this->getDuration()->getTime()
             / $this->getMeasureDuration($measure);
    }

    /**
     * Calculate total measure duration
     */
    private function getMeasureDuration(Measure $measure): int
    {
        return array_reduce(
            $measure->getBeats(),
            $this->getMeasureTimeHelper(),
            0
        );
    }

    /**
     * Provides a closure helper for measure time calculation
     */
    private function getMeasureTimeHelper(): callable
    {
        return function ($carry, $item) {
            return $carry
                 + $item
                    ->getVoice($this->getIndex())
                    ->getDuration()
                    ->getTime();
        };
    }

    public function __clone()
    {
        $this->duration = clone $this->duration;

        foreach ($this->notes as $index => $item) {
            $this->notes[$index] = clone $item;
        }
    }
}
