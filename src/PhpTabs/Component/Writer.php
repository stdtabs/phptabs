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

class Writer
{
  /** @var string $path */
  private $path;

  /** @var Tablature */
  private $tablature;

  /** @var array A list of supported writers */
  private $writers = array(
    'gp3' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro3Writer',
    'gp4' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro4Writer',
    'gp5' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro5Writer',
    'mid' => 'PhpTabs\\Writer\\Midi\\MidiWriter',
    'midi'=> 'PhpTabs\\Writer\\Midi\\MidiWriter'
  );

  /**
   * @param \PhpTabs\Component\Tablature $tablature
   */
  public function __construct(Tablature $tablature)
  {
    $this->tablature = $tablature;
  }

  /**
   * Builds content in $format
   * 
   * @param string $format
   *
   * @return string A binary chain
   * 
   * @throws \Exception if output format is not supported
   */
  public function build($format)
  {
    if (!isset($this->writers[$format]))
    {
      $message = sprintf('Output format %s is not supported', $format);

      throw new Exception($message);
    }

    return (new $this->writers[$format]($this->tablature->getSong()))->getContent();
  }

  /**
   * Outputs internal model into buffer or a file
   *
   * @param string $path
   * 
   * @return mixed boolean|string
   * 
   * @throws \Exception if an incorrect destination path is supplied
   */
  public function save($path = null)
  {
    if ($path == 'php://output')
    {
      print($this->build($this->tablature->getFormat()));

      return true;
    }
    elseif (null === $path)
    {
      return $this->build($this->tablature->getFormat());
    }
    elseif (is_string($path))
    {
      $parts = pathinfo($path);

      if (!isset($parts['basename'], $parts['extension']))
      {
        $message = sprintf('Destination path %s is not complete', $path);

        throw new Exception($message);
      }

      $this->path = $path;

      return $this->record($this->build($parts['extension']));
    }

    throw new Exception('Save path is not allowed');
  }

  /**
   * Records $content into a file
   * 
   * @param string $content binary chain
   * 
   * @throws \Exception If content can not be written
   */
  private function record($content)
  {
    $dir = pathinfo($this->path, PATHINFO_DIRNAME);

    if (!is_dir($dir) || !is_writable($dir))
    {
      throw new Exception('Save directory error');
    }
    elseif (is_file($this->path) && !is_writable($this->path))
    {
      $message = sprintf('File "%s" still exists and is not writable', $this->path);

      throw new Exception($message);
    }

    file_put_contents($this->path, $content);
  }
}
