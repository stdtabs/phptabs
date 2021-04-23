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
 * @uses Chord
 * @uses Duration
 * @uses Measure
 * @uses Stroke
 * @uses Text
 * @uses Voice
 */
final class Beat
{
    /**
     * Number of voices to set
     *
     * @const MAX_VOICES
     */
    const MAX_VOICES = 2;

    private $start = Duration::QUARTER_TIME;
    private $measure;
    private $chord;
    private $text;
    private $voices = [];
    private $stroke;

    public function __construct()
    {
        $this->stroke = new Stroke();

        for ($i = 0; $i < self::MAX_VOICES; $i++) {
            $this->setVoice($i, new Voice($i));
        }
    }

    /**
     * Get parent Measure
     */
    public function getMeasure(): Measure
    {
        return $this->measure;
    }

    /**
     * Set parent measure
     */
    public function setMeasure(Measure $measure): void
    {
        $this->measure = $measure;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $start): void
    {
        $this->start = $start;
    }

    public function setVoice(int $index, Voice $voice): void
    {
        if ($index >= 0) {
            $this->voices[$index] = $voice;
            $this->voices[$index]->setBeat($this);
        }
    }

    public function getVoice(int $index): Voice
    {
        if (isset($this->voices[$index])) {
            return $this->voices[$index];
        }

        throw new Exception(
            sprintf(
                'Index %s does not exist',
                $index
            )
        );
    }

    public function getVoices(): array
    {
        return $this->voices;
    }

    public function countVoices(): int
    {
        return count($this->voices);
    }

    public function setChord(Chord $chord): void
    {
        $this->chord = $chord;
        $this->chord->setBeat($this);
    }

    public function getChord(): ?Chord
    {
        return $this->chord;
    }

    public function removeChord(): void
    {
        $this->chord = null;
    }

    public function getText(): ?Text
    {
        return $this->text;
    }

    public function setText(Text $text): void
    {
        $this->text = $text;
        $this->text->setBeat($this);
    }

    public function removeText(): void
    {
        $this->text = null;
    }

    public function isChordBeat(): bool
    {
        return $this->chord !== null;
    }

    public function isTextBeat(): bool
    {
        return $this->text !== null;
    }

    public function getStroke(): Stroke
    {
        return $this->stroke;
    }

    public function isRestBeat(): bool
    {
        for ($v = 0; $v < $this->countVoices(); $v++) {
            $voice = $this->getVoice($v);

            if (!$voice->isEmpty() && !$voice->isRestVoice()) {
                return false;
            }
        }

        return true;
    }

    public function __clone()
    {
        if (!is_null($this->chord)) {
            $this->chord = clone $this->chord;
        }

        if (!is_null($this->text)) {
            $this->text = clone $this->text;
        }

        if (!is_null($this->stroke)) {
            $this->stroke = clone $this->stroke;
        }

        foreach ($this->voices as $index => $item) {
            $this->voices[$index] = clone $item;
        }
    }
}
