<?php

namespace PhpTabs\Component;

use Exception;
use PhpTabs\Model\Channel;
use PhpTabs\Model\ChannelNames;
use PhpTabs\Model\Song;

class Tablature
{
  const DEFAULT_FILE_FORMAT = 'gp3';

  /** @var string error message */
  private $error = '';

  /** @var object Song */
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
   * @param string $error
   */
  public function setError($message)
  {
    $this->error = $message;
  }

  /**
   * @return string Error set during build operations
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * @return boolean true if there was an error. Otherwise, false.
   */
  public function hasError()
  {
    return $this->error !== '';
  }

  /**
   * Sets Song wrapper
   *
   * @param \PhpTabs\Model\Song $song
   */
  public function setSong(Song $song)
  {
    $this->song = $song;
  }

  /**
   * Gets a Song
   *
   * @return \PhpTabs\Model\Song
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
    if (!($count = $this->countChannels()))
    {
      return array();
    }

    $instruments = array();

    for ($i = 0; $i < $count; $i++)
    {
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
   * @return integer
   */
  public function countInstruments()
  {
    return $this->countChannels();
  }

  /**
   * Gets instrument by channelId
   *
   * @param integer $index
   * 
   * @return array
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
   * Dumps a song into an array
   * 
   * @param string $format
   * 
   * @return array
   */
  public function dump($format = null)
  { 
    $dumper = new Dumper($this);

    return null === $format
      ? $dumper->dump() : $dumper->dump($format);
  }

  /**
   * Writes a song into a file
   * 
   * @param string $filename
   * 
   * @return mixed boolean|string
   * 
   * @throws Exception If tablature container contains error
   */
  public function save($filename = null)
  {
    if ($this->hasError())
    {
      $message = sprintf('%s(): %s'
        , __METHOD__
        , 'Current data cannot be saved because parsing has encountered an error'
      );

      throw new Exception($message);
    }

    return (new Writer($this))->save($filename);
  }

  /**
   * Builds a binary starting from Music DOM
   *
   * @param string $format
   * 
   * @return string A binary chain
   */
  public function convert($format = null)
  {
    if (null === $format)
    {
      $format = $this->getFormat();
    }

    return (new Writer($this))->build($format);
  }

  /**
   * Overloads with $song methods
   * 
   * @param string $name method
   * @param array $arguments
   * 
   * @return mixed
   */
  public function __call($name, $arguments)
  {
    if (!method_exists($this->song, $name))
    {
      $message = sprintf('Song has no method called "%s"', $name);

      trigger_error($message, E_USER_ERROR);
    }

    switch(count($arguments))
    {
      case 0: return $this->song->$name();
      case 1: return $this->song->$name($arguments[0]);
      default:
        $message = sprintf('%s method does not support %d arguments',
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
