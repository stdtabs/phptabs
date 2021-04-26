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

use PhpTabs\Component\Exporter\ExporterBase;
use PhpTabs\Component\Serializer\Text;
use PhpTabs\Component\Serializer\Xml;
use PhpTabs\Component\Serializer\Yaml;
use PhpTabs\Music\Song;

final class Exporter extends ExporterBase
{
    /**
     * @var \PhpTabs\Music\Song
     */
    protected $song;

    /**
     * @param Tablature $tablature The tablature to export
     */
    public function __construct(Tablature $tablature)
    {
        $this->song = $tablature->getSong();
    }

    /**
     * Give access to the song property
     */
    protected function getSong(): Song
    {
        return $this->song;
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
            $flags | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE
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
