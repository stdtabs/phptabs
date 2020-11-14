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
use PhpTabs\Component\Tablature;

class Writer
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
     * @var array A list of supported writers
     */
    private $writers = [
        'gp3' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro3Writer',
        'gp4' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro4Writer',
        'gp5' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro5Writer',
        'mid' => 'PhpTabs\\Writer\\Midi\\MidiWriter',
        'midi'=> 'PhpTabs\\Writer\\Midi\\MidiWriter',
        'json'=> 'PhpTabs\\Writer\\Json\\JsonWriter',
        'ser' => 'PhpTabs\\Writer\\Serialized\\SerializedWriter',
        'txt' => 'PhpTabs\\Writer\\Text\\TextWriter',
        'text'=> 'PhpTabs\\Writer\\Text\\TextWriter',
        'xml' => 'PhpTabs\\Writer\\Xml\\XmlWriter',
        'yaml'=> 'PhpTabs\\Writer\\Yaml\\YamlWriter',
        'yml' => 'PhpTabs\\Writer\\Yaml\\YamlWriter',
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
    public function save(string $path, string $format = null): bool
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

        if (!is_dir($dir) || !is_writable($dir)) {
            throw new Exception('Save directory error');
        } elseif (is_file($this->path) && !is_writable($this->path)) {
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
