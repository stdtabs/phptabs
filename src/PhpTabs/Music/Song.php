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

namespace PhpTabs\Music;

use Exception;

final class Song extends SongBase
{
    /**
     * Get the list of instruments
     *
     * @return array<key,array<int,string>>
     */
    public function getInstruments(): array
    {
        $count = $this->countChannels();
        if (!$count) {
            return [];
        }

        $instruments = [];

        for ($i = 0; $i < $count; $i++) {
            $instruments[$i] = [
                'id'    => $this->getChannel($i)->getProgram(),
                'name'  => ChannelNames::DEFAULT_NAMES[$this->getChannel($i)->getProgram()]
            ];
        }

        return $instruments;
    }

    /**
     * Counts instruments
     */
    public function countInstruments(): int
    {
        return $this->countChannels();
    }

    /**
     * Gets an instrument by channelId
     *
     * @return array<int,array<int,string>>
     */
    public function getInstrument(int $index): ?array
    {
        return $this->getChannel($index) instanceof Channel
            ? [ 'id'    => $this->getChannel($index)->getProgram(),
                'name'  => ChannelNames::DEFAULT_NAMES[$this->getChannel($index)->getProgram()]
            ]
            : null;
    }

    public function addMeasureHeader(MeasureHeader $measureHeader): void
    {
        $measureHeader->setSong($this);
        $this->measureHeaders[] = $measureHeader;
    }

    public function removeMeasureHeader(MeasureHeader $measureHeader): void
    {
        $this->measureHeaders = array_filter(
            $this->measureHeaders,
            static function ($item) use ($measureHeader) {
                return $item->getNumber() !== $measureHeader->getNumber();
            }
        );

        $this->measureHeaders = array_values($this->measureHeaders);
    }

    public function getMeasureHeader(int $index): MeasureHeader
    {
        if (isset($this->measureHeaders[$index])) {
            return $this->measureHeaders[$index];
        }

        throw new Exception(
            sprintf(
                'MeasureHeader at index "%s" does not exist',
                $index
            )
        );
    }

    /**
     * @return array<MeasureHeader>
     */
    public function getMeasureHeaders(): array
    {
        return $this->measureHeaders;
    }

    public function addTrack(Track $track): void
    {
        $track->setSong($this);
        $this->tracks[] = $track;
        // Track number has default value
        if ($track->getNumber() === 0) {
            $track->setNumber($this->countTracks());
        }
    }

    public function moveTrack(int $index, Track $track): void
    {
        $this->removeTrack($track);
        $this->tracks[$index] = $track;
    }

    public function removeTrack(Track $track): void
    {
        $this->tracks = array_filter(
            $this->tracks,
            static function ($item) use ($track) {
                return $item->getNumber() !== $track->getNumber();
            }
        );

        $this->tracks = array_values($this->tracks);
    }

    public function getTrack(int $index): Track
    {
        if (isset($this->tracks[$index])) {
            return $this->tracks[$index];
        }

        throw new Exception(
            sprintf(
                'Index %s does not exist',
                $index
            )
        );
    }

    /**
     * @return array<Track>
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    /**
     * @param int|\PhpTabs\Music\Channel $index
     */
    public function addChannel($index, ?Channel $channel = null): void
    {
        if ($index instanceof Channel) {
            $this->channels[] = $index;
        } elseif (is_int($index)) {
            array_splice($this->channels, $index, 0, $channel);
        }
    }

    public function moveChannel(int $index, Channel $channel): void
    {
        $this->addChannel($index, $channel);
    }

    public function removeChannel(Channel $channel): void
    {
        $this->channels = array_filter(
            $this->channels,
            static function ($item) use ($channel) {
                return $item->getId() !== $channel->getId();
            }
        );

        $this->channels = array_values($this->channels);
    }

    public function getChannel(int $index): Channel
    {
        if (isset($this->channels[$index])) {
            return $this->channels[$index];
        }

        throw new Exception(
            sprintf(
                'Index %s does not exist',
                $index
            )
        );
    }

    public function getChannelById(int $channelId): ?Channel
    {
        $channels = $this->getChannels();

        foreach ($channels as $channel) {
            if ($channel->getId() === $channelId) {
                return $channel;
            }
        }

        return null;
    }

    /**
     * @return array<Channel>
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    public function __clone()
    {
        foreach ($this->measureHeaders as $index => $header) {
            $this->measureHeaders[$index] = clone $header;
        }

        foreach ($this->channels as $index => $channel) {
            $this->channels[$index] = clone $channel;
        }

        foreach ($this->tracks as $index => $track) {
            $this->tracks[$index] = clone $track;
        }
    }
}
