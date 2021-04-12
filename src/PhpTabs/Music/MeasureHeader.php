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

/**
 * @uses Duration
 * @uses Tempo
 * @uses TimeSignature
 */
class MeasureHeader
{
    const TRIPLET_FEEL_NONE      = 1;
    const TRIPLET_FEEL_EIGHTH    = 2;
    const TRIPLET_FEEL_SIXTEENTH = 3;

    private $number            = 1;
    private $marker            = null;
    private $repeatOpen        = false;
    private $repeatAlternative = 0;
    private $repeatClose       = 0;
    private $start;
    private $timeSignature;
    private $tempo;
    private $tripletFeel;
    private $song;

    public function __construct()
    {
        $this->start         = Duration::QUARTER_TIME;
        $this->timeSignature = new TimeSignature();
        $this->tempo         = new Tempo();
        $this->tripletFeel   = MeasureHeader::TRIPLET_FEEL_NONE;
        $this->checkMarker();
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
        $this->checkMarker();
    }

    public function getRepeatClose(): int
    {
        return $this->repeatClose;
    }

    public function setRepeatClose(int $repeatClose): void
    {
        $this->repeatClose = $repeatClose;
    }

    public function getRepeatAlternative(): int
    {
        return $this->repeatAlternative;
    }

    /**
     * bitwise value 1 TO 8.
     * (1 << AlternativeNumber)
     */
    public function setRepeatAlternative(int $repeatAlternative): void
    {
        $this->repeatAlternative = $repeatAlternative;
    }

    public function isRepeatOpen(): bool
    {
        return $this->repeatOpen;
    }

    /**
     * @param int $repeatOpen
     */
    public function setRepeatOpen(int $repeatOpen): void
    {
        $this->repeatOpen = (boolean)$repeatOpen;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $start): void
    {
        $this->start = $start;
    }

    public function getTripletFeel(): int
    {
        return $this->tripletFeel;
    }

    public function setTripletFeel(int $tripletFeel): void
    {
        $this->tripletFeel = intval($tripletFeel);
    }

    public function getTempo(): Tempo
    {
        return $this->tempo;
    }

    public function setTempo(Tempo $tempo): void
    {
        $this->tempo = $tempo;
    }

    public function getTimeSignature(): TimeSignature
    {
        return $this->timeSignature;
    }

    public function setTimeSignature(TimeSignature $timeSignature): void
    {
        $this->timeSignature = $timeSignature;
    }

    public function getMarker(): ?Marker
    {
        return $this->marker;
    }

    public function setMarker(Marker $marker): void
    {
        $this->marker = $marker;
    }

    public function hasMarker(): bool
    {
        return $this->getMarker() !== null;
    }

    private function checkMarker(): void
    {
        if ($this->hasMarker()) {
            $this->marker->setMeasure($this->getNumber());
        }
    }

    public function getLength(): int
    {
        return $this->getTimeSignature()->getNumerator()
             * $this->getTimeSignature()->getDenominator()->getTime();
    }

    public function getSong(): Song
    {
        return $this->song;
    }

    public function setSong(Song $song): void
    {
        $this->song = $song;
    }

    public function copyFrom(MeasureHeader $header): void
    {
        $this->setNumber($header->getNumber());
        $this->setStart($header->getStart());
        $this->setRepeatOpen($header->isRepeatOpen());
        $this->setRepeatAlternative($header->getRepeatAlternative());
        $this->setRepeatClose($header->getRepeatClose());
        $this->setTripletFeel($header->getTripletFeel());
        $this->getTimeSignature()->copyFrom($header->getTimeSignature());
        $this->getTempo()->copyFrom($header->getTempo());
        $this->setMarker($header->hasMarker() ? (clone $header->getMarker()) : null);
        $this->checkMarker();
    }

    public function __clone()
    {
        $this->timeSignature = clone $this->timeSignature;
        $this->tempo         = clone $this->tempo ;
    }
}
