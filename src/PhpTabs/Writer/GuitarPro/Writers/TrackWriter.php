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
use PhpTabs\Music\Track;

final class TrackWriter
{
    /**
     * @var WriterInterface
     */
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    private function writeTrack(Track $track): void
    {
        $channel = $this->writer->getChannelRoute($track->getChannelId());

        $flags = 0;
        if ($track->getSong()->getChannelById($track->getChannelId())->isPercussionChannel()) {
            $flags |= 0x01;
        }

        $this->writer->writeUnsignedByte($flags);
        $this->writer->writeStringByte($track->getName(), 40);
        $this->writer->writeInt(count($track->getStrings()));

        for ($i = 0; $i < 7; $i++) {
            $value = 0;
            if (count($track->getStrings()) > $i) {
                $string = $track->getStrings()[$i];
                $value = $string->getValue();
            }
            $this->writer->writeInt($value);
        }

        $this->writer->writeInt(1);
        $this->writer->writeInt($channel->getChannel1() + 1);
        $this->writer->writeInt($channel->getChannel2() + 1);
        $this->writer->writeInt(24);
        $this->writer->writeInt(min(max($track->getOffset(), 0), 12));
        $this->writer->writeColor($track->getColor());
    }

    public function writeTracks(Song $song): void
    {
        foreach ($song->getTracks() as $track) {
            $this->writeTrack($track);
        }
    }
}
