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

namespace PhpTabs\Writer\GuitarPro\Writers;

use PhpTabs\Component\WriterInterface;
use PhpTabs\Music\Beat;
use PhpTabs\Music\DivisionType;
use PhpTabs\Music\Measure;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Stroke;
use PhpTabs\Music\Voice;

final class BeatWriter
{
    /**
     * @var WriterInterface
     */
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function writeBeat(Beat $beat, Measure $measure, bool $changeTempo): void
    {
        $voice = $beat->getVoice(0);
        $duration = $voice->getDuration();

        $effect = $this->createEffect($voice);

        $flags = 0;

        if ($duration->isDotted() || $duration->isDoubleDotted()) {
            $flags |= 0x01;
        }

        if ($beat->isChordBeat()) {
            $flags |= 0x02;
        }

        if ($beat->isTextBeat()) {
            $flags |= 0x04;
        }

        if ($beat->getStroke()->getDirection() !== Stroke::STROKE_NONE) {
            $flags |= 0x08;
        } elseif ($effect->isTremoloBar()
            || $effect->isTapping()
            || $effect->isSlapping()
            || $effect->isPopping()
            || $effect->isFadeIn()
        ) {
            $flags |= 0x08;
        }

        if (! $duration->getDivision()->isEqual(DivisionType::normal())) {
            $flags |= 0x20;
        }

        if ($changeTempo) {
            $flags |= 0x10;
        }

        if ($voice->isRestVoice()) {
            $flags |= 0x40;
        }

        $this->writer->writeUnsignedByte($flags);

        if (($flags & 0x40) !== 0) {
            $this->writer->writeUnsignedByte(2);
        }

        $this->writer->writeByte($this->writer->parseDuration($duration));

        if (($flags & 0x20) !== 0) {
            $this->writer->writeInt($duration->getDivision()->getEnters());
        }

        if (($flags & 0x02) !== 0) {
            $this->writer->writeChord($beat->getChord());
        }

        if (($flags & 0x04) !== 0) {
            $this->writer->writeText($beat->getText());
        }

        if (($flags & 0x08) !== 0) {
            $this->writer->getWriter('BeatEffectWriter')->writeBeatEffects($beat, $effect);
        }

        if (($flags & 0x10) !== 0) {
            $this->writer->writeMixChange($measure->getTempo());
        }

        $stringFlags = 0;

        if (! $voice->isRestVoice()) {
            $countNotes = $voice->countNotes();
            for ($i = 0; $i < $countNotes; $i++) {
                $playedNote = $voice->getNote($i);
                $string = 7 - $playedNote->getString();
                $stringFlags |= 1 << $string;
            }
        }

        $this->writer->writeUnsignedByte($stringFlags);

        for ($i = 6; $i >= 0; $i--) {
            if (($stringFlags & (1 << $i)) !== 0) {
                $countNotes = $voice->countNotes();
                for ($n = 0; $n < $countNotes; $n++) {
                    $playedNote = $voice->getNote($n);
                    if ($playedNote->getString() === 6 - $i + 1) {
                        $this->writer->getWriter('NoteWriter')->writeNote($playedNote);
                        break;
                    }
                }
            }
        }
    }

    /**
     * Create a NoteEffect for handling beat effect
     */
    public function createEffect(Voice $voice): NoteEffect
    {
        $effect = new NoteEffect();

        $countNotes = $voice->countNotes();
        for ($i = 0; $i < $countNotes; $i++) {
            $playedNote = $voice->getNote($i);

            if ($playedNote->getEffect()->isFadeIn()) {
                $effect->setFadeIn(true);
            }

            if ($playedNote->getEffect()->isTremoloBar()) {
                $effect->setTremoloBar(clone $playedNote->getEffect()->getTremoloBar());
            }

            if ($playedNote->getEffect()->isTapping()) {
                $effect->setTapping(true);
            }

            if ($playedNote->getEffect()->isSlapping()) {
                $effect->setSlapping(true);
            }

            if ($playedNote->getEffect()->isPopping()) {
                $effect->setPopping(true);
            }
        }

        return $effect;
    }
}
