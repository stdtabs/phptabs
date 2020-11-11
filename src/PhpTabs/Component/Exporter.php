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
     * Export to a PHP array
     */
    public function toArray(): array
    {
        return $this->exportSong();
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
     * Export to a TXT string
     */
    public function toText(): string
    {
        return (new Text())->serialize($this->exportSong());
    }

    /**
     * Export to XML string
     */
    public function toXml(): string
    {
        return (new Xml())->serialize($this->exportSong());
    }

    /**
     * Export to YAML string
     */
    public function toYaml(): string
    {
        return (new Yaml())->serialize($this->exportSong());
    }
}
