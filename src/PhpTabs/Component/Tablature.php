<?php

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
     * An error message
     *
     * @var string
     */
    private $error = '';

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
     * Sets an error message
     */
    public function setError(string $message): void
    {
        $this->error = $message;
    }

    /**
     * @return string An error set during build operations
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return bool true if there was an error. Otherwise, false.
     */
    public function hasError(): string
    {
        return $this->error !== '';
    }

    /**
     * Sets Song wrapper
     */
    public function setSong(Song $song): void
    {
        $this->song = $song;
    }

    /**
     * Gets a Song
     */
    public function getSong(): Song
    {
        return $this->song;
    }

    /**
     * Gets the list of instruments
     */
    public function getInstruments(): array
    {
        if (!($count = $this->countChannels())) {
            return array();
        }

        $instruments = array();

        for ($i = 0; $i < $count; $i++) {
            $instruments[$i] = array(
                'id'    => $this->getChannel($i)->getProgram(),
                'name'  => ChannelNames::$defaultNames[$this->getChannel($i)->getProgram()]
            );
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
     */
    public function getInstrument(int $index): ?array
    {
        return $this->getChannel($index) instanceof Channel
        ? array(
        'id'    => $this->getChannel($index)->getProgram(),
        'name'  => ChannelNames::$defaultNames[$this->getChannel($index)->getProgram()]
        ) : null;
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
     * Export one track + song context
     *
     * @param  int    $index   Target track
     * @param  string $format  Desired format
     * @param  int    $options Export options
     * @return string|array
     */
    public function exportTrack(int $index, string $format = null, $options = null)
    {
        if (null === $this->getSong()->getTrack($index)) {
            throw new Exception("Track n°$index does not exist");
        }

        $exporter = new Exporter($this);
        $exporter->setFilter('trackIndex', $index);

        return $exporter->export($format, $options);
    }

    /**
     * Render a song into a string
     */
    public function getRenderer(string $format = null): RendererInterface
    {
        return (new Renderer($this))->setFormat($format);
    }

    /**
     * Writes a song into a file
     *
     * @return mixed bool|string
     * @throws \Exception If tablature container contains error
     */
    public function save(string $filename = null)
    {
        if ($this->hasError()) {
            $message = sprintf(
                '%s(): %s',
                __METHOD__,
                'Current data cannot be saved because parsing has encountered an error'
            );

            throw new Exception($message);
        }

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

            trigger_error($message, E_USER_ERROR);
        }

        if (count($arguments) < 2) {
            return $this->song->$name(...$arguments);
        }

        $message = sprintf(
            '%s method does not support %d arguments',
            __METHOD__,
            count($arguments)
        );

        trigger_error($message, E_USER_ERROR);
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
