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
use PhpTabs\PhpTabs;
use PhpTabs\Music\Channel;
use PhpTabs\Music\ChannelNames;
use PhpTabs\Music\Song;

class Tablature
{
  const DEFAULT_FILE_FORMAT = 'gp3';

  /** @var string An error message */
  private $error = '';

  /** @var \PhpTabs\Music\Song*/
  private $song;

  /** @var string $format */
  private $format;

  public function __construct()
  {
    $this->setSong(new Song());
    $this->setFormat(self::DEFAULT_FILE_FORMAT);
  }

  /**
   * Sets an error message
   *
   * @param string $message
   */
  public function setError($message)
  {
    $this->error = $message;
  }

  /**
   * @return string An error set during build operations
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * @return bool true if there was an error. Otherwise, false.
   */
  public function hasError()
  {
    return $this->error !== '';
  }

  /**
   * Sets Song wrapper
   *
   * @param \PhpTabs\Music\Song $song
   */
  public function setSong(Song $song)
  {
    $this->song = $song;
  }

  /**
   * Gets a Song
   *
   * @return \PhpTabs\Music\Song
   */
  public function getSong()
  {
    return $this->song;
  }

  /**
   * Gets the list of instruments
   * 
   * @return array
   */
  public function getInstruments()
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
   *
   * @return int
   */
  public function countInstruments()
  {
    return $this->countChannels();
  }

  /**
   * Gets an instrument by channelId
   *
   * @param  int $index
   * @return null|array
   */
  public function getInstrument($index)
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
  public function export($format = null, $options = null)
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
  public function exportTrack($index, $format = null, $options = null)
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
   * 
   * @param  string $format
   * @return \PhpTabs\Component\Renderer\RendererInterface
   */
  public function getRenderer($format = null)
  {
    return (new Renderer($this))->setFormat($format);
  }

  /**
   * Writes a song into a file
   * 
   * @param  string $filename
   * @return mixed bool|string
   * @throws \Exception If tablature container contains error
   */
  public function save($filename = null)
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
   * Builds a binary starting from Music DOM
   *
   * @param  string $format
   * @return string A binary chain
   */
  public function convert($format = null)
  {
    if (null === $format) {
      $format = $this->getFormat();
    }

    return (new Writer($this))->build($format);
  }

  /**
   * Overloads with $song methods
   * 
   * @param  string $name method
   * @param  array $arguments
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

    switch (count($arguments)) {
      case 0: return $this->song->$name();
      case 1: return $this->song->$name($arguments[0]);
      default:
        $message = sprintf(
            '%s method does not support %d arguments',
            __METHOD__,
            count($arguments)
        );

        trigger_error($message, E_USER_ERROR);
    }
  }

  /**
   * Memorize original format
   * 
   * @param string $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }

  /**
   * Returns orignal format
   * 
   * @return string $format
   */
  public function getFormat()
  {
    return $this->format;
  }
}
