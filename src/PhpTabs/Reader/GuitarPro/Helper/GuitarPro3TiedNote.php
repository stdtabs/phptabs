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

use PhpTabs\Music\Track;

final class GuitarPro3TiedNote extends AbstractReader
{
    /**
     * @param int $string String on which note has started
     *
     * @return int tied note value
     */
    public function getTiedNoteValue(int $string, Track $track): int
    {
        $measureCount = $track->countMeasures();

        if ($measureCount > 0) {
            for ($m = $measureCount - 1; $m >= 0; $m--) {
                $measure = $track->getMeasure($m);

                $countBeats = $measure->countBeats();
                for ($b = $countBeats - 1; $b >= 0; $b--) {
                    $beat = $measure->getBeat($b);
                    $voice = $beat->getVoice(0);

                    $countNotes = $voice->countNotes();
                    for ($n = 0; $n < $countNotes; $n++) {
                        $note = $voice->getNote($n);

                        if ($note->getString() === $string) {
                            return $note->getValue();
                        }
                    }
                }
            }
        }

        return -1;
    }
}
