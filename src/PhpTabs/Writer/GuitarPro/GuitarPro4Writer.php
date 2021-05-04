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

namespace PhpTabs\Writer\GuitarPro;

use Exception;
use PhpTabs\Music\Chord;
use PhpTabs\Music\Marker;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Text;

final class GuitarPro4Writer extends GuitarProWriterBase
{
    private const VERSION = 'FICHIER GUITAR PRO v4.00';

    public function __construct(Song $song)
    {
        parent::__construct();

        if ($song->isEmpty()) {
            throw new Exception('Song is empty');
        }

        $this->configureChannelRouter($song);
        $header = $song->getMeasureHeader(0);
        $this->writeStringByte(self::VERSION, 30);
        $this->writeInformations($song);
        $this->writeBoolean(
            $header->getTripletFeel() === MeasureHeader::TRIPLET_FEEL_EIGHTH
        );
        $this->getWriter('LyricsWriter')->writeLyrics($song);
        $this->writeInt($header->getTempo()->getValue());
        $this->writeInt(0);
        $this->writeByte(0);
        $this->getWriter('ChannelWriter')->writeChannels($song);
        $this->writeInt($song->countMeasureHeaders());
        $this->writeInt($song->countTracks());
        $this->getWriter('MeasureHeaderWriter')->writeMeasureHeaders($song);
        $this->getWriter('TrackWriter')->writeTracks($song);
        $this->getWriter('MeasureWriter')->writeMeasures($song, clone $header->getTempo());
    }

    public function writeChord(Chord $chord): void
    {
        $this->writeUnsignedByte(0x01);
        $this->skipBytes(16);
        $this->writeStringByte($chord->getName(), 21);
        $this->skipBytes(4);
        $this->writeInt($chord->getFirstFret());

        for ($i = 0; $i < 7; $i++) {
            $this->writeInt($i < $chord->countStrings() ? $chord->getFretValue($i) : -1);
        }

        $this->skipBytes(32);
    }

    private function writeInformations(Song $song): void
    {
        $this->writeStringByteSizeOfInteger((string) $song->getName());
        $this->writeStringByteSizeOfInteger('');
        $this->writeStringByteSizeOfInteger((string) $song->getArtist());
        $this->writeStringByteSizeOfInteger((string) $song->getAlbum());
        $this->writeStringByteSizeOfInteger((string) $song->getAuthor());
        $this->writeStringByteSizeOfInteger((string) $song->getCopyright());
        $this->writeStringByteSizeOfInteger((string) $song->getWriter());
        $this->writeStringByteSizeOfInteger('');

        $comments = $this->toCommentLines((string) $song->getComments());

        $countComments = count($comments);
        $this->writeInt($countComments);

        for ($i = 0; $i < $countComments; $i++) {
            $this->writeStringByteSizeOfInteger($comments[$i]);
        }
    }

    public function writeMarker(Marker $marker): void
    {
        $this->writeStringByteSizeOfInteger($marker->getTitle());
        $this->writeColor($marker->getColor());
    }

    public function writeMixChange(Tempo $tempo): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->writeByte(-1);
        }

        $this->writeInt($tempo->getValue());
        $this->writeByte(0);
        $this->writeUnsignedByte(1);
    }

    /**
     * @return array<string>
     */
    private function toCommentLines(string $comments): array
    {
        $lines = [];
        $line = $comments;

        while (strlen($line) > 127) {
            $lines[] = substr($line, 0, 127);
            $line = substr($line, 127);
        }

        $lines[] = $line;

        return $lines;
    }

    public function writeText(Text $text): void
    {
        $this->writeStringByteSizeOfInteger($text->getValue());
    }
}
