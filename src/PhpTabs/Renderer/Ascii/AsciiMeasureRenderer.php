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

namespace PhpTabs\Renderer\Ascii;

use Exception;
use PhpTabs\Music\Beat;
use PhpTabs\Music\Duration;
use PhpTabs\Music\Measure;
use PhpTabs\Music\Note;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Voice;

final class AsciiMeasureRenderer
{
    /**
     * Measure container
     *
     * @var \PhpTabs\Music\Measure
     */
    private $measure;

    /**
     * String container
     *
     * @var \PhpTabs\Music\TabString
     */
    private $string;

    /**
     * Global writer
     *
     * @var \PhpTabs\Renderer\Ascii\AsciiBase
     */
    private $writer;

    /**
     * Constructor
     */
    public function __construct(AsciiBase $writer, Measure $measure, TabString $string)
    {
        $this->measure = $measure;
        $this->string  = $string;
        $this->writer  = $writer;
    }

    /**
     * Append a measure, ASCII formatted
     */
    public function render(): void
    {
        $this->writer->drawBarSegment();
        $this->writer->drawStringSegments(1);
        $stringCount = $this->measure->getTrack()->countStrings();
        $drawRestBeatStrings = [$stringCount/2, 1 + $stringCount/2];

        // Get first beat
        $beat = $this->measure->getBeat(0);

        while ($beat !== null) {
            $outLength = 0;

            // Notes
            $note = $this->getNote($beat, $this->string->getNumber());
            if ($note !== null) {
                $outLength = mb_strlen($this->getNoteValue($note)) - 1;
                $this->writer->drawNote($this->getNoteValue($note));

            // Rest beat
            } elseif ($beat->isRestBeat() && in_array($this->string->getNumber(), $drawRestBeatStrings)) {
                $this->writer->drawNote(AsciiRenderer::RESTBEAT_CHR);

            // Draw a space
            } else {
                $this->writer->drawStringSegments(1);
            }

            $nextBeat = $this->getNextBeat($this->measure->getBeats(), $beat);

            $length = ($nextBeat !== null
                ? $nextBeat->getStart() - $beat->getStart()
                : $this->measure->getStart() + $this->measure->getLength() - $beat->getStart()
            );

            $this->writer->drawStringSegments($this->getDurationScaping($length) - $outLength);

            $beat = $nextBeat;
        }
    }

    /**
     * Get following beat
     *
     * @todo FIXME $next is always null
     */
    public function getNextBeat(array $beats, Beat $beat): ?Beat
    {
        $next = null;

        foreach ($beats as $current) {
            if ($current->getStart() > $beat->getStart()) {
                if ($next === null) {
                    return $current;
                }

                if ($current->getStart() < $next->getStart()) {
                    return $current;
                }
            }
        }

        return $next;
    }

    /**
     * Get note value
     */
    private function getNoteValue(Note $note): string
    {
        if ($note->getEffect()->isDeadNote()) {
            return AsciiRenderer::DEADNOTE_CHR;
        }

        return strval($note->getValue());
    }

    /**
     * Get corresponding note for a beat
     */
    private function getNote(Beat $beat, int $stringNumber): ?Note
    {
        foreach ($beat->getVoices() as $voice) {
            if (!$voice->isEmpty()) {
                $note = $this->getNoteByVoice($voice, $stringNumber);

                if ($note !== null) {
                    return $note;
                }
            }
        }

        return null;
    }

    /**
     * Get related note for a Voice
     */
    private function getNoteByVoice(Voice $voice, int $stringNumber): ?Note
    {
        foreach ($voice->getNotes() as $note) {
            if ($note->getString() === $stringNumber) {
                return $note;
            }
        }

        return null;
    }

    /**
     * Get duration number of spaces
     */
    private function getDurationScaping(int $length): int
    {
        switch (true) {
            case $length <= Duration::QUARTER_TIME / 8:
                return 2;
            case $length <= Duration::QUARTER_TIME / 4:
                return 3;
            case $length <= Duration::QUARTER_TIME / 2:
                return 4;
            case $length <= Duration::QUARTER_TIME:
                return 5;
            case $length <= Duration::QUARTER_TIME * 2:
                return 6;
        }

        return 7;
    }
}
