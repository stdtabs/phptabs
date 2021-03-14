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

use PhpTabs\Music\Duration;
use PhpTabs\Music\Measure;
use PhpTabs\Music\Song;
use PhpTabs\Music\Tempo;

class GuitarPro5Measures extends AbstractReader
{
    /**
     * Loop on mesures to read
     */
    public function readMeasures(Song $song, int $measures, int $tracks, int $tempoValue): void
    {
        $tempo = new Tempo();
        $tempo->setValue($tempoValue);
        $start = Duration::QUARTER_TIME;

        for ($i = 0; $i < $measures; $i++) {
            $header = $song->getMeasureHeader($i);
            $header->setStart($start);

            for ($j = 0; $j < $tracks; $j++) {
                $track = $song->getTrack($j);
                $measure = new Measure($header);

                $track->addMeasure($measure);
                $this->reader->factory('GuitarPro5Measure')->readMeasure($measure, $track, $tempo);

                if ($i != $measures - 1 || $j != $tracks - 1) {
                    $this->reader->skip();
                }
            }

            $header->getTempo()->copyFrom($tempo);
            $start += $header->getLength();
        }
    }
}
