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

namespace PhpTabs\Component;

use Exception;
use PhpTabs\Component\Renderer\RendererInterface;
use PhpTabs\PhpTabs;
use PhpTabs\Music\Channel;
use PhpTabs\Music\ChannelNames;
use PhpTabs\Music\Song;

class Tablature
{
    const DEFAULT_FILE_FORMAT = 'gp3';

    /**
     * Entry point of the music model
     *
     * @var \PhpTabs\Music\Song
     */
    private $song;

    /**
     * Tablature original format
     *
     * @var string $format
     */
    private $format;

    public function __construct()
    {
        $this->setSong(new Song());
        $this->setFormat(self::DEFAULT_FILE_FORMAT);
    }

    /**
     * Sets Song wrapper
     */
    public function setSong(Song $song): void
    {
        $this->song = $song;
    }

    /**
     * Get a Song
     */
    public function getSong(): Song
    {
        return $this->song;
    }

    /**
     * Export a song into an array
     *
     * @param  string $format
     * @param  mixed  $options Flags for some exported formats
     * @return array|string
     */
    public function export(string $format = null, $options = null)
    {
        $exporter = new Exporter($this);

        return $exporter->export($format, $options);
    }

    /**
     * Render a song as an ASCII tabs
     */
    public function toAscii(array $options = []): string
    {
        return $this->getRenderer('ascii')
                    ->setOptions($options)
                    ->render();
    }

    /**
     * Rebuild a new PhpTabs with only the targeted tracks
     */
    public function sliceTracks(int $fromTrackIndex, int $toTrackIndex): PhpTabs
    {
        $tabs = new PhpTabs();
        $tabs->copyFrom($this->getSong());

        $keepTracks   = [];
        $keepChannels = [];

        for ($i = 0; $i < $tabs->countTracks(); $i++) {
            if ($i >= $fromTrackIndex && $i <= $toTrackIndex) {
                array_push($keepTracks, $tabs->getTrack($i)->getNumber());
                array_push($keepChannels, $tabs->getTrack($i)->getChannelId());
            }
        }

        // Clean tracks
        foreach ($tabs->getTracks() as $track) {
            if (!in_array($track->getNumber(), $keepTracks)) {
                $tabs->removeTrack($track);
            }
        }

        // Clean channels
        foreach ($tabs->getChannels() as $channel) {
            if (!in_array($channel->getId(), $keepChannels)) {
                $tabs->removeChannel($channel);
            }
        }

        return $tabs;
    }

    /**
     * Rebuild a new PhpTabs with only the targeted track
     */
    public function onlyTrack(int $trackIndex): PhpTabs
    {
        return $this->sliceTracks($trackIndex, $trackIndex);
    }

    /**
     * Rebuild a new PhpTabs with only the targeted measures
     */
    public function sliceMeasures(int $fromMeasureIndex, int $toMeasureIndex): PhpTabs
    {
        $tabs = new PhpTabs();
        $tabs->copyFrom($this->getSong());

        $keepMeasures = [];

        // Get the measures to keep
        for ($i = 0; $i < $tabs->countMeasureHeaders(); $i++) {
            if ($i >= $fromMeasureIndex && $i <= $toMeasureIndex) {
                array_push($keepMeasures, $tabs->getMeasureHeader($i)->getNumber());
            }
        }

        // Clean measure headers
        foreach ($tabs->getMeasureHeaders() as $measureHeader) {

            if (!in_array($measureHeader->getNumber(), $keepMeasures)) {
                $tabs->removeMeasureHeader($measureHeader);

                // Clean measure for each track
                foreach ($tabs->getTracks() as $track) {
                    $track->removeMeasure($measureHeader->getNumber());
                }
            }
        }

        return $tabs;
    }

    /**
     * Rebuild a new PhpTabs with only the targeted measure
     * for each track
     */
    public function onlyMeasure(int $measureIndex): PhpTabs
    {
        return $this->sliceMeasures($measureIndex, $measureIndex);
    }

    /**
     * Prepare a renderer
     */
    public function getRenderer(string $format = null): RendererInterface
    {
        return (new Renderer($this))->setFormat($format);
    }

    /**
     * Writes a song into a file
     *
     * @return mixed bool|string
     */
    public function save(string $filename = null)
    {
        return (new Writer($this))->save($filename);
    }

    /**
     * Builds a binary starting from Music model
     */
    public function convert(string $format = null): string
    {
        if (null === $format) {
            $format = $this->getFormat();
        }

        return (new Writer($this))->build($format);
    }

    /**
     * Overloads with $song methods
     *
     * @param  string $name      method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->song, $name)) {
            $message = sprintf(
                'Song has no method called "%s"',
                $name
            );

            throw new Exception($message);
        }

        if (count($arguments) > 2) {
            $message = sprintf(
                '%s method does not support %d arguments',
                __METHOD__,
                count($arguments)
            );

            throw new Exception($message);
        }

        return $this->song->$name(...$arguments);
    }

    /**
     * Memorize original format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Returns orignal format
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
