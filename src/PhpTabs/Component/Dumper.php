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
use PhpTabs\Component\Dumper\DumperBase;
use PhpTabs\Component\Serializer\Text;
use PhpTabs\Component\Serializer\Xml;
use PhpTabs\Component\Serializer\Yaml;

class Dumper extends DumperBase
{
  /** @var \Phptabs\Model\Song */
  protected $song;

  /**
   * @param \PhpTabs\Component\Tablature The tablature to dump
   */
  public function __construct(Tablature $tablature)
  {
    $this->song = $tablature->getSong();
  }

  /**
   * Returns a representation of the song into a desired format
   * 
   * @param  string $format
   *  - array       : a raw PHP array
   *  - xml         : an XML string
   *  - json        : a JSON string
   *  - var_export  : a raw PHP array as string
   *  - serialize   : a PHP serialized
   *  - text        : a non standardized text
   *  - txt         : same as text
   *  - yaml        : a YAML representation
   *  - yml         : same as yaml
   *
   * @return string|array
   * @throws \Exception if format is not supported
   */
  public function dump($format = 'array')
  {
    switch ($format)
    {
      case 'array':
        return $this->dumpSong();
      case 'xml':
        return (new Xml())->serialize($this->dump());
      case 'json':
        return json_encode($this->dump());
      case 'var_export':
        return var_export($this->dump(), true);
      case 'serialize':
        return serialize($this->dump());
      case 'text':
      case 'txt':
        return (new Text())->serialize($this->dump());
      case 'yaml':
      case 'yml':
        return (new Yaml())->serialize($this->dump());
      default:
        $message = sprintf('%s does not support "%s" format', __METHOD__, $format);
        throw new Exception($message);
        break;
    }
  }
}
