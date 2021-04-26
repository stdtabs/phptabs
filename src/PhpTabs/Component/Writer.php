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

namespace PhpTabs\Component;

use Exception;
use PhpTabs\Writer\GuitarPro\GuitarPro3Writer;
use PhpTabs\Writer\GuitarPro\GuitarPro4Writer;
use PhpTabs\Writer\GuitarPro\GuitarPro5Writer;
use PhpTabs\Writer\Json\JsonWriter;
use PhpTabs\Writer\Midi\MidiWriter;
use PhpTabs\Writer\Serialized\SerializedWriter;
use PhpTabs\Writer\Text\TextWriter;
use PhpTabs\Writer\Xml\XmlWriter;
use PhpTabs\Writer\Yaml\YamlWriter;

final class Writer
{
    /**
     * @var string $path
     */
    private $path;

    /**
     * @var Tablature
     */
    private $tablature;

    /**
     * @var array A list of available writers and their bridges
     */
    private $writers = [
        'gp3'  => GuitarPro3Writer::class,
        'gp4'  => GuitarPro4Writer::class,
        'gp5'  => GuitarPro5Writer::class,
        'json' => JsonWriter::class,
        'mid'  => MidiWriter::class,
        'midi' => MidiWriter::class,
        'ser'  => SerializedWriter::class,
        'text' => TextWriter::class,
        'txt'  => TextWriter::class,
        'xml'  => XmlWriter::class,
        'yaml' => YamlWriter::class,
        'yml'  => YamlWriter::class,
    ];

    public function __construct(Tablature $tablature)
    {
        $this->tablature = $tablature;
    }

    /**
     * Build content in $format
     *
     * @throws \Exception if output format is not supported
     */
    public function build(string $format): string
    {
        if (!isset($this->writers[$format])) {
            $message = sprintf('Output format %s is not supported', $format);

            throw new Exception($message);
        }

        $writer = new $this->writers[$format]($this->tablature->getSong());

        return $writer->getContent();
    }

    /**
     * Outputs internal model into buffer or a file
     *
     * @throws \Exception if an incorrect destination path is supplied
     */
    public function save(string $path, ?string $format = null): bool
    {
        $parts = pathinfo($path);

        if (!isset($parts['basename'], $parts['extension'])) {
            $message = sprintf(
                'Destination path %s is not complete',
                $path
            );

            throw new Exception($message);
        }

        $this->path = $path;

        return $this->record(
            $this->build(
                is_null($format)
                    ? $parts['extension']
                    : $format
            )
        );
    }

    /**
     * Records $content into a file
     *
     * @throws \Exception If content can not be written
     */
    private function record(string $content): bool
    {
        $dir = pathinfo($this->path, PATHINFO_DIRNAME);

        if (! is_dir($dir) || ! is_writable($dir)) {
            throw new Exception('Save directory error');
        }

        if (is_file($this->path) && ! is_writable($this->path)) {
            // @codeCoverageIgnoreStart
            $message = sprintf(
                'File "%s" already exists and is not writable',
                $this->path
            );

            throw new Exception($message);
            // @codeCoverageIgnoreEnd
        }

        return file_put_contents($this->path, $content) !== false;
    }
}
