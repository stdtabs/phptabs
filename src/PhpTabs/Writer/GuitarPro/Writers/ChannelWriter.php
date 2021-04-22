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
use PhpTabs\Music\Channel;
use PhpTabs\Music\Song;

class ChannelWriter
{
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function writeChannels(Song $song): void
    {
        $channels = $this->makeChannels($song);

        array_walk(
            $channels, function ($channel): void {
                $this->writer->writeInt($channel->getProgram());
                $this->writer->writeByte($this->toChannelByte($channel->getVolume()));
                $this->writer->writeByte($this->toChannelByte($channel->getBalance()));
                $this->writer->writeByte($this->toChannelByte($channel->getChorus()));
                $this->writer->writeByte($this->toChannelByte($channel->getReverb()));
                $this->writer->writeByte($this->toChannelByte($channel->getPhaser()));
                $this->writer->writeByte($this->toChannelByte($channel->getTremolo()));
                $this->writer->writeBytes([0, 0]);
            }
        );
    }

    private function makeChannels(Song $song): array
    {
        $channels = [];

        for ($i = 0; $i < 64; $i++) {
            $channels[$i] = new Channel();
            $channels[$i]->setProgram(
                $i == Channel::DEFAULT_PERCUSSION_CHANNEL
                ? Channel::DEFAULT_PERCUSSION_PROGRAM : 24
            );
            $channels[$i]->setVolume(13);
            $channels[$i]->setBalance(8);
            $channels[$i]->setChorus(0);
            $channels[$i]->setReverb(0);
            $channels[$i]->setPhaser(0);
            $channels[$i]->setTremolo(0);
        }

        foreach ($song->getChannels() as $channel) {
            $channelRoute = $this->writer->getChannelRoute($channel->getId());
            $channels[$channelRoute->getChannel1()]->setProgram($channel->getProgram());
            $channels[$channelRoute->getChannel1()]->setVolume($channel->getVolume());
            $channels[$channelRoute->getChannel1()]->setBalance($channel->getBalance());

            $channels[$channelRoute->getChannel2()]->setProgram($channel->getProgram());
            $channels[$channelRoute->getChannel2()]->setVolume($channel->getVolume());
            $channels[$channelRoute->getChannel2()]->setBalance($channel->getBalance());
        }

        return $channels;
    }

    private function toChannelByte(int $short): int
    {
        return intval(($short + 1) / 8);
    }
}
