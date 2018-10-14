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
   * @var array $filters
   */
  protected $filters = [];

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
   *  - xml         : an XML string
   *  - json        : a JSON string
   *  - var_export  : a raw PHP array as string
   *  - serialize   : a PHP serialized
   *  - text        : a non standardized text
   *  - txt         : same as text
   *  - yaml        : a YAML representation
   *  - yml         : same as yaml
   * @param  mixed $options Some flags for exported formats
   * @return string|array
   * @throws \Exception if format is not supported
   */
  public function export($format = null, $options = null)
  {
    switch ($format) {
      case null:
      case 'array':
        return $this->exportSong();
      case 'xml':
        return (new Xml())->serialize($this->exportSong());
      case 'json':
        return $this->toJson(is_int($options) ? $options : 0);
      case 'var_export':
        return var_export($this->exportSong(), true);
      case 'serialize':
        return serialize($this->exportSong());
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
   * 
   * @param  int $flags
   * @return string
   */
  private function toJson($flags)
  {
    // >=PHP 5.5.0, export Skip JSON error 5 Malformed UTF-8 
    // characters, possibly incorrectly encoded
    if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
      $flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
    }

    return json_encode($this->export(), $flags);
  }

  /**
   * Set a filter before exporting
   * 
   * @param string $type
   * @param mixed  $filter
   */
  public function setFilter($type, $filter)
  {
    $this->filters[$type] = $filter;
  }
}
