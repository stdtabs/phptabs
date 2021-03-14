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

use PhpTabs\Music\Lyric;
use PhpTabs\Music\Song;
use PhpTabs\Music\Track;
use PhpTabs\Music\TabString;

class GuitarPro4Track extends AbstractReader
{
    /**
     * Reads track informations
     */
    public function readTrack(Song $song, array $channels, Lyric $lyrics): Track
    {
        $track = new Track();
        $track->setLyrics($lyrics);

        $this->reader->readUnsignedByte();

        $track->setName($this->reader->readStringByte(40));

        $stringCount = $this->reader->readInt();

        for ($i = 0; $i < 7; $i++) {
            $tuning = $this->reader->readInt();

            if ($stringCount > $i) {
                $string = new TabString();
                $string->setNumber($i + 1);
                $string->setValue($tuning);

                $track->addString($string);
            }
        }

        $this->reader->readInt();
        $this->reader->factory('GuitarProChannel')->readChannel($song, $track, $channels);
        $this->reader->readInt();

        $track->setOffset($this->reader->readInt());

        $this->reader->factory('GuitarProColor')->readColor($track->getColor());

        return $track;
    }
}
