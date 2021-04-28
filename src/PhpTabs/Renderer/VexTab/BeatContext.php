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

namespace PhpTabs\Renderer\VexTab;

use PhpTabs\Music\Beat;
use PhpTabs\Music\Note;
use PhpTabs\Music\Stroke;

final class BeatContext
{
    /**
     * Referenced Beat
     *
     * @var \PhpTabs\Music\Beat
     */
    private $beat;

    /**
     * Tuplet counter
     *
     * @var int
     */
    private $tupletCounter = 0;

    /**
     * @var bool|null
     */
    private $isChordBeat;

    /**
     * Constructor
     * Parse beat informations for current and later usage
     */
    public function __construct(Beat $beat)
    {
        $this->beat = $beat;
    }

    /**
     * Should be processed as a Chord beat
     */
    public function isChordBeat(): bool
    {
        if (!is_null($this->isChordBeat)) {
            return $this->isChordBeat;
        }

        $voice = $this->beat->getVoice(0);

        if (!$voice->countNotes()) {
            return $this->isChordBeat = false;
        }

        if ($voice->countNotes() > 1) {
            return $this->isChordBeat = true;
        }

        if ($voice->getNote(0)->getEffect()->isVibrato()
            && $this->beat->getStroke()->getDirection() !== Stroke::STROKE_NONE
        ) {
            return $this->isChordBeat = true;
        }

        return $this->isChordBeat = false;
    }

    /**
     * Get effects from last beat for current note
     *  - s
     *  - h
     *  - p
     */
    public function getPrevPrefix(Note $note): string
    {
        return $this->getSlide($note)
             . $this->getHammer($note);
    }

    /**
     * Get effects that have to be prefixed for current note
     *  - t
     *  - T
     */
    public function getPrefix(Note $note): string
    {
        return $this->getTied($note)
             . $this->getTapping($note);
    }

    /**
     * Get effects that have to be suffixed for current note
     * - b ie: 6b7b8/1
     * - v
     * - V
     * If it's a single note
     * - u
     * - d
     */
    public function getSuffix(Note $note): string
    {
        return $this->getBend($note)
             . $this->getVibrato($note)
          // . $this->getHarshVibrato($note)
             . (!$this->isChordBeat() ? $this->getStroke() : '');
    }

    /**
     * Get suffix for a chord beat
     * - u
     * - d
     */
    public function getChordSuffix(): string
    {
        return $this->getStroke();
    }

    /**
     * return a tuplet symbol
     */
    public function getTuplet(BeatContext $lastBeatContext): string
    {
        $enters = $this->beat
            ->getVoice(0)
            ->getDuration()
            ->getDivision()
            ->getEnters();

        if ($enters === 1) {
            return '';
        }

        $lastCounter = $lastBeatContext->getTupletCounter();
        $lastCounter++;
        
        if ($lastCounter === $enters) {
            return sprintf(
                '^%d^ ',
                $enters
            );
        }

        $this->tupletCounter = $lastCounter;

        return '';
    }

    /**
     * Get tuplet counter
     */
    public function getTupletCounter(): int
    {
        return $this->tupletCounter;
    }

    /**
     * Find corresponding string and return a slide effect if existing
     */
    private function getSlide(Note $note): string
    {
        foreach ($this->beat->getVoice(0)->getNotes() as $prevNote) {
            if ($prevNote->getString() === $note->getString()) {
                return $prevNote->getEffect()->isSlide()
                    ? 's'
                    : '';
            }
        }

        return '';
    }

    /**
     * Find corresponding string and return a hammer-on or a pull-off
     *  effect if existing
     */
    private function getHammer(Note $note): string
    {
        foreach ($this->beat->getVoice(0)->getNotes() as $prevNote) {
            if ($prevNote->getString() === $note->getString()
                && $prevNote->getEffect()->isHammer()
            ) {
                return $prevNote->getValue() >= $note->getValue()
                    ? 'p'
                    : 'h';
            }
        }

        return '';
    }

    /**
     * Return a tied symbol if existing
     */
    private function getTied(Note $note): string
    {
        return $note->isTiedNote()
            ? 'T'
            : '';
    }

    /**
     * Return a tapping symbol if existing
     */
    private function getTapping(Note $note): string
    {
        return $note->getEffect()->isTapping()
            ? 't'
            : '';
    }


    /**
     * Return a bend symbol if existing
     */
    private function getBend(Note $note): string
    {
        if (!$note->getEffect()->isBend()) {
            return '';
        }

        $value         = '';
        $lastBendValue = $note->getValue();

        foreach ($note->getEffect()->getBend()->getPoints() as $point) {

            $bendValue = $note->getValue() + intval($point->getValue() / 2);
            //  must skip if;
            // - first bend is the same note as starting note
            // - bend is standing on the same point
            if ($bendValue !== $lastBendValue) {
                $lastBendValue = $bendValue;

                $value .= sprintf(
                    'b%d',
                    $bendValue
                );
            }
        }

        return $value;
    }

    /**
     * Return a vibrato symbol if existing
     */
    private function getVibrato(Note $note): string
    {
        return $note->getEffect()->isVibrato()
            ? 'v'
            : '';
    }

    /**
     * Return a harsh vibrato symbol if existing
     *
     * @todo implement this feature
     */
    private function getHarshVibrato(): string
    {
        return '';
    }

    /**
     * Return a stroke symbol if existing
     */
    private function getStroke(): string
    {
        if ($this->beat->getStroke()->getDirection() === Stroke::STROKE_NONE) {
            return '';
        }

        return $this->beat->getStroke()->getDirection() === Stroke::STROKE_UP
            ? 'u'
            : 'd';
    }
}
