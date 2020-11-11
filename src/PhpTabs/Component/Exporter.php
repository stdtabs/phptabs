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
use PhpTabs\Component\Tablature;
use PhpTabs\Component\Exporter\ExporterBase;
use PhpTabs\Component\Serializer\Text;
use PhpTabs\Component\Serializer\Xml;
use PhpTabs\Component\Serializer\Yaml;

class Exporter extends ExporterBase
{
    /**
     * @var \PhpTabs\Music\Song
     */
    protected $song;

    /**
     * @param \PhpTabs\Component\Tablature $tablature The tablature to export
     */
    public function __construct(Tablature $tablature)
    {
        $this->song = $tablature->getSong();
    }

    /**
     * Returns a representation of the song into a desired format
     *
     * @param  null|string $format
     *  - array       : a raw PHP array
     *  - var_export  : a raw PHP array as string
     *  - serialize   : a PHP serialized
     *  - text        : a non standardized text
     *  - txt         : same as text
     *  - yaml        : a YAML representation
     *  - yml         : same as yaml
     *
     * @todo Depreciate all formats that do not return array
     * 
     * @param  mixed       $options Some flags for exported formats
     * @return string|array
     * @throws \Exception if format is not supported
     */
    public function export($format = null, $options = null)
    {
        switch ($format) {
            case null:
            case 'array':
                return $this->exportSong();
            case 'var_export':
                return var_export($this->exportSong(), true);
            case 'text':
            case 'txt':
                return (new Text())->serialize($this->exportSong());
            case 'yaml':
            case 'yml':
                return (new Yaml())->serialize($this->exportSong());
        }

        $message = sprintf('%s does not support "%s" format', __METHOD__, $format);
        throw new Exception($message);
    }

    /**
     * Export to JSON string
     */
    public function toJson(int $flags = 0): string
    {
        // >=PHP 5.5.0, export Skip JSON error 5 Malformed UTF-8
        // characters, possibly incorrectly encoded
        return json_encode(
            $this->exportSong(),
            $flags | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
    }

    /**
     * Export to PHP serialized string
     */
    public function toSerialized(): string
    {
        return serialize($this->exportSong());
    }

    /**
     * Export to XML string
     */
    public function toXml(): string
    {
        return (new Xml())->serialize($this->exportSong());
    }
}
