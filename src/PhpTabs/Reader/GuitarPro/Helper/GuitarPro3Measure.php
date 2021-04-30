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

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Measure;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Track;

final class GuitarPro3Measure extends AbstractReader
{
    /**
     * Reads a Measure
     */
    public function readMeasure(Measure $measure, Track $track, Tempo $tempo): void
    {
        $nextNoteStart = intval($measure->getStart());
        $numberOfBeats = $this->reader->readInt();

        for ($i = 0; $i < $numberOfBeats; $i++) {
            $nextNoteStart += $this->reader->factory('GuitarPro3Beat')->readBeat(
                $nextNoteStart,
                $measure,
                $track,
                $tempo
            );
        }

        $measure->setClef(
            $this->reader->factory('GuitarProClef')->getClef($track)
        );
        $measure->setKeySignature($this->reader->getKeySignature());
    }
}
