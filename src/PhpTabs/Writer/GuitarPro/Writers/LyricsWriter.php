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
use PhpTabs\Music\Song;

final class LyricsWriter
{
    /**
     * @var WriterInterface
     */
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function writeLyrics(Song $song): void
    {
        $lyricTrack = null;
        $tracks = $song->getTracks();

        foreach ($tracks as $track) {
            if (! $track->getLyrics()->isEmpty()) {
                $lyricTrack = $track;
                break;
            }
        }

        $this->writer->writeInt(is_null($lyricTrack) ? 0 : $lyricTrack->getNumber());
        $this->writer->writeInt(is_null($lyricTrack) ? 0 : $lyricTrack->getLyrics()->getFrom());
        $this->writer->writeStringInteger(
            is_null($lyricTrack) ? '' : $lyricTrack->getLyrics()->getLyrics()
        );

        for ($i = 0; $i < 4; $i++) {
            $this->writer->writeInt(is_null($lyricTrack) ? 0 : 1);
            $this->writer->writeStringInteger('');
        }
    }
}
