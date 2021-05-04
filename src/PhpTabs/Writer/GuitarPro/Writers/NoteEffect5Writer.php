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
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Velocities;

final class NoteEffect5Writer
{
    /**
     * @var WriterInterface
     */
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function writeNoteEffects(NoteEffect $effect): void
    {
        $flags1 = $this->writer->getWriter('NoteEffectWriter')->parseFlag1($effect);
        $flags2 = $this->writer->getWriter('NoteEffectWriter')->parseFlag2($effect);

        $this->writer->writeUnsignedByte($flags1);
        $this->writer->writeUnsignedByte($flags2);

        if (($flags1 & 0x01) !== 0) {
            $this->writer->getWriter('NoteEffectWriter')->writeBend($effect->getBend());
        }

        if (($flags1 & 0x10) !== 0) {
            $this->writeGrace($effect->getGrace());
        }

        if (($flags2 & 0x04) !== 0) {
            $this->writer->getWriter('NoteEffectWriter')->writeTremoloPicking($effect->getTremoloPicking());
        }

        if (($flags2 & 0x08) !== 0) {
            $this->writer->writeByte(1);
        }

        if (($flags2 & 0x10) !== 0) {
            $this->writer->writeByte(1);
        }

        if (($flags2 & 0x20) !== 0) {
            $this->writer->getWriter('NoteEffectWriter')->writeTrill($effect->getTrill());
        }
    }

    private function writeGrace(EffectGrace $grace): void
    {
        $this->writer->writeUnsignedByte($grace->getFret());

        $this->writer->writeUnsignedByte(
            intval((($grace->getDynamic() - Velocities::MIN_VELOCITY) / Velocities::VELOCITY_INCREMENT) + 1)
        );

        $this->writer->getWriter('NoteEffectWriter')->writeTransition(
            $grace->getTransition()
        );

        $this->writer->writeUnsignedByte($grace->getDuration());

        $this->writer->writeUnsignedByte(
            ($grace->isDead() ? 0x01 : 0) | ($grace->isOnBeat() ? 0x02 : 0)
        );
    }
}
