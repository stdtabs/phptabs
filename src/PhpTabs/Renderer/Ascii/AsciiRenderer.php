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

namespace PhpTabs\Renderer\Ascii;

use Exception;
use PhpTabs\Component\Renderer\RendererHelper;
use PhpTabs\Component\Renderer\RendererInterface;
use PhpTabs\Music\Track;
use PhpTabs\Music\Song;

class AsciiRenderer extends RendererHelper
{
    /**
     * Characters
     */
    const DEADNOTE_CHR       = 'X';
    const RESTBEAT_CHR       = '%';
    const BAR_SEGMENT_CHR    = '|';
    const STRING_SEGMENT_CHR = '-';

    /**
     * Song container
     *
     * @var \PhpTabs\Music\Song
     */
    private $song;

    /**
     * Writer
     *
     * @var \PhpTabs\Renderer\Ascii\AsciiBase
     */
    private $writer;

    /**
     * Constructor
     */
    public function __construct(Song $song)
    {
        $this->song   = $song;
        $this->writer = new AsciiBase();
    }

    /**
     * Draw a song, a list of staves, ASCII formatted
     *
     * @api
     * @since 0.6.0
     */
    public function render(int $index = null): string
    {
        // Check if track exists for this index
        if (!is_null($index)) {
             $track = $this->song->getTrack($index);
        }

        if ($this->getOption('songHeader')) {
            $this->writer->drawStringLine("Title: "   . $this->song->getName());
            $this->writer->drawStringLine("Album: "   . $this->song->getAlbum());
            $this->writer->drawStringLine("Artist: "  . $this->song->getArtist());
            $this->writer->drawStringLine("Author: "  . $this->song->getAuthor());
        }

        foreach ($this->song->getTracks() as $currentIndex => $track) {
            if ($index === null || $index === $currentIndex) {
                $this->writer->nextLine();
                $this->writeTrack($track);
                $this->writer->nextLine();
            }
        }

        return $this->writer->output();
    }

    /**
     * Get writer
     */
    public function getWriter(): AsciiBase
    {
        return $this->writer;
    }

    /**
     * Convert a track as a VexTab text
     */
    private function writeTrack(Track $track): void
    {
        (new AsciiTrackRenderer($this, $track))->render();
    }
}
