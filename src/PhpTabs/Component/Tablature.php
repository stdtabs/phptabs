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
     * @var string
     */
    private $format;

    public function __construct()
    {
        $this->setSong(new Song());
        $this->setFormat(self::DEFAULT_FILE_FORMAT);
    }

    /**
     * Set Song wrapper
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
     * Export a song as a PHP array
     */
    public function toArray(): array
    {
        return $this->getExporter()->toArray();
    }

    /**
     * Get exporter tool
     */
    public function getExporter(): Exporter
    {
        return new Exporter($this);
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
     * Get a Guitar Pro 3 representation
     */
    public function toGuitarPro3(): string
    {
        return $this->convert('gp3');
    }

    /**
     * Get a Guitar Pro 4 representation
     */
    public function toGuitarPro4(): string
    {
        return $this->convert('gp4');
    }

    /**
     * Get a Guitar Pro 5 representation
     */
    public function toGuitarPro5(): string
    {
        return $this->convert('gp5');
    }

    /**
     * Get a JSON representation
     */
    public function toJson(int $flags = 0): string
    {
        return $this->getExporter()->toJson($flags);
    }

    /**
     * Get a MIDI representation
     */
    public function toMidi(): string
    {
        return $this->convert('mid');
    }

    /**
     * Get a PHP serialized representation
     */
    public function toSerialized(): string
    {
        return $this->convert('ser');
    }

    /**
     * Get a TEXT representation
     */
    public function toText(): string
    {
        return $this->convert('txt');
    }

    /**
     * Get an XML representation
     */
    public function toXml(): string
    {
        return $this->convert('xml');
    }

    /**
     * Get a YAML representation
     */
    public function toYaml(): string
    {
        return $this->convert('yml');
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
     * Prepare a writer
     */
    public function writer(): Writer
    {
        return new Writer($this);
    }

    /**
     * Write a song into a file
     */
    public function save(string $filename): bool
    {
        return $this->writer()->save($filename);
    }

    /**
     * Build a binary starting from Music model
     */
    public function convert(string $format = null): string
    {
        if (null === $format) {
            $format = $this->getFormat();
        }

        return $this->writer()->build($format);
    }

    /**
     * Memorize original format
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * Return orignal format
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
