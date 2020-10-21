<?php

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
use PhpTabs\Music\Measure;
use PhpTabs\Music\Song;
use PhpTabs\Music\Tempo;
use PhpTabs\Share\MeasureVoiceJoiner;

class MeasureWriter
{
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function writeMeasures(Song $song, Tempo $tempo): void
    {
        foreach ($song->getMeasureHeaders() as $index => $header) {

            foreach ($song->getTracks() as $track) {
                $this->writeMeasure(
                    $track->getMeasure($index),
                    $header->getTempo()->getValue() != $tempo->getValue()
                );
            }

            $tempo->copyFrom($header->getTempo());
        }
    }

    private function writeMeasure(Measure $srcMeasure, bool $changeTempo): void
    {
        $measure = (new MeasureVoiceJoiner($srcMeasure))->process();

        $this->writer->writeInt($measure->countBeats());

        foreach ($measure->getBeats() as $index => $beat) {
            $this->writer->getWriter('BeatWriter')->writeBeat(
                $beat,
                $measure,
                ($changeTempo && $index == 0)
            );
        }
    }
}
