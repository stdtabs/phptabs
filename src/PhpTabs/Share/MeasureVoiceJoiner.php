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

namespace PhpTabs\Share;

use PhpTabs\Music\Duration;
use PhpTabs\Music\Measure;

final class MeasureVoiceJoiner
{
    /**
     * @var Measure
     */
    private $measure;

    public function __construct(Measure $measure)
    {
        $this->measure = clone $measure;
        $this->measure->setTrack($measure->getTrack());
    }

    public function process(): Measure
    {
        $this->orderBeats();
        $this->joinBeats();

        return $this->measure;
    }

    public function joinBeats(): void
    {
        $previous = null;
        $finish = true;

        $measureStart = $this->measure->getStart();
        $measureEnd = $measureStart + $this->measure->getLength();

        $countBeats = $this->measure->countBeats();
        for ($i = 0; $i <  $countBeats; $i++) {
            $beat = $this->measure->getBeat($i);
            $voice = $beat->getVoice(0);

            $countVoices = $beat->countVoices();
            for ($v = 1; $v < $countVoices; $v++) {
                $currentVoice = $beat->getVoice($v);

                if (!$currentVoice->isEmpty()) {
                    $countNotes = $currentVoice->countNotes();
                    for ($n = 0; $n < $countNotes; $n++) {
                        $note = $currentVoice->getNote($n);
                        $voice->addNote($note);
                    }
                }
            }

            if ($voice->isEmpty()) {
                $this->measure->removeBeat($beat);
                $finish = false;
                break;
            }

            $beatStart = $beat->getStart();

            if ($previous !== null) {
                $previousStart = $previous->getStart();

                $previousBestDuration = null;
                $countVoices = $previous->countVoices();
                for ($v = 0; $v < $countVoices; $v++) {
                    $previousVoice = $previous->getVoice($v);

                    if (!$previousVoice->isEmpty()) {
                        $length = $previousVoice->getDuration()->getTime();

                        if ($previousStart + $length <= $beatStart) {
                            if ($previousBestDuration === null || $length > $previousBestDuration->getTime()) {
                                  $previousBestDuration = $previousVoice->getDuration();
                            }
                        }
                    }
                }

                if ($previousBestDuration !== null) {
                    $previous->getVoice(0)->getDuration()->copyFrom($previousBestDuration);
                } else {
                    if ($voice->isRestVoice()) {
                        $this->measure->removeBeat($beat);
                        $finish = false;
                        break;
                    }
                    $duration = Duration::fromTime($beatStart - $previousStart);
                    $previous->getVoice(0)->getDuration()->copyFrom($duration);
                }
            }

            $beatBestDuration = null;
            $count = $beat->countVoices();
            for ($v = 0; $v < $count; $v++) {
                $currentVoice = $beat->getVoice($v);

                if (!$currentVoice->isEmpty()) {
                    $length = $currentVoice->getDuration()->getTime();

                    if ($beatStart + $length <= $measureEnd) {
                        if (is_null($beatBestDuration) || $length > $beatBestDuration->getTime()) {
                            $beatBestDuration = $currentVoice->getDuration();
                        }
                    }
                }
            }

            if (is_null($beatBestDuration)) {
                if ($voice->isRestVoice()) {
                    $this->measure->removeBeat($beat);
                    $finish = false;
                    break;
                }
                $duration = Duration::fromTime($measureEnd - $beatStart);
                $voice->getDuration()->copyFrom($duration);
            }
            $previous = $beat;
        }

        if (!$finish) {
            $this->joinBeats();
        }
    }

    public function orderBeats(): void
    {
        $count =  $this->measure->countBeats();
        for ($i = 0; $i < $count; $i++) {
            $minBeat = null;

            for ($j = $i; $j < $count; $j++) {
                $beat = $this->measure->getBeat($j);

                if (is_null($minBeat) || $beat->getStart() < $minBeat->getStart()) {
                    $minBeat = $beat;
                }
            }

            $this->measure->moveBeat($i, $minBeat);
        }
    }
}
