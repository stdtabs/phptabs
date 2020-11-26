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

use Exception;

/**
 * @uses Beat
 * @uses MeasureHeader
 * @uses Track
 */
class Measure
{
    const CLEF_TREBLE = 1;
    const CLEF_BASS   = 2;
    const CLEF_TENOR  = 3;
    const CLEF_ALTO   = 4;

    const DEFAULT_CLEF = 1;
    const DEFAULT_KEY_SIGNATURE = 0;

    private $header;
    private $track;
    private $clef;
    private $keySignature;
    private $beats = [];

    public function __construct(MeasureHeader $header)
    {
        $this->header = $header;
        $this->clef = self::DEFAULT_CLEF;
        $this->keySignature = self::DEFAULT_KEY_SIGNATURE;
    }

    public function getTrack(): Track
    {
        return $this->track;
    }

    public function setTrack(Track $track): void
    {
        $this->track = $track;
    }

    public function getClef(): int
    {
        return $this->clef;
    }

    public function setClef(int $clef): void
    {
        $this->clef = $clef;
    }

    public function getKeySignature(): int
    {
        return $this->keySignature;
    }

    public function setKeySignature(int $keySignature): void
    {
        $this->keySignature = $keySignature;
    }

    public function getBeats(): array
    {
        return $this->beats;
    }

    public function addBeat(Beat $beat): void
    {
        $beat->setMeasure($this);

        $this->beats[] = $beat;
    }

    public function moveBeat(int $index, Beat $beat): void
    {
        $this->removeBeat($beat);

        array_splice($this->beats, $index, 0, array($beat));
    }

    public function removeBeat(Beat $beat): void
    {
        foreach ($this->beats as $k => $v) {
            if ($v == $beat) {
                array_splice($this->beats, $k, 1);

                return;
            }
        }
    }

    /**
     * @throw Exception if beat does not exist
     */
    public function getBeat(int $index): Beat
    {
        if (isset($this->beats[$index])) {
            return $this->beats[$index];
        }

        throw new Exception(
            sprintf(
                'Index %s does not exist',
                $index
            )
        );
    }

    public function countBeats(): int
    {
        return count($this->beats);
    }

    public function getHeader(): MeasureHeader
    {
        return $this->header;
    }

    public function setHeader(MeasureHeader $header): void
    {
        $this->header = $header;
    }

    public function getNumber(): int
    {
        return $this->header->getNumber();
    }

    public function getRepeatClose(): int
    {
        return $this->header->getRepeatClose();
    }

    public function getStart(): int
    {
        return intval($this->header->getStart());
    }

    public function getTempo(): Tempo
    {
        return $this->header->getTempo();
    }

    public function getTimeSignature(): TimeSignature
    {
        return $this->header->getTimeSignature();
    }

    public function isRepeatOpen(): bool
    {
        return $this->header->isRepeatOpen();
    }

    public function getTripletFeel(): int
    {
        return $this->header->getTripletFeel();
    }

    public function getLength(): int
    {
        return $this->header->getLength();
    }

    public function getMarker(): Marker
    {
        return $this->header->getMarker();
    }

    public function hasMarker(): bool
    {
        return $this->header->hasMarker();
    }

    public function clear(): void
    {
        $this->beats = [];
    }

    public function getBeatByStart(int $start): Beat
    {
        $beat = array_reduce(
            $this->beats,
            function ($carry, $beat) use ($start) {
                return $beat->getStart() == $start
                    ? $beat : $carry;
            }
        );

        if (!($beat instanceof Beat)) {
            $beat = new Beat();
            $beat->setStart($start);
            $this->addBeat($beat);
        }

        return $beat;
    }

    public function copyFrom(Measure $measure): void
    {
        $this->clear();
        $this->clef         = $measure->getClef();
        $this->keySignature = $measure->getKeySignature();

        foreach ($measure->getBeats() as $beat) {
            $this->addBeat(clone $beat);
        }
    }

    public function __clone()
    {
        foreach ($this->beats as $index => $item) {
            $this->beats[$index] = clone $item;
        }
    }
}
