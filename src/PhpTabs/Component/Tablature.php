<?php

namespace PhpTabs\Component;

use PhpTabs\Model\Song;
use PhpTabs\Model\Channel;
use PhpTabs\Model\ChannelNames;

class Tablature
{ 
  /** @var string error message */
  private $error = '';

  /** @var object Song */
  private $song = '';

  /**
   * Tablature constructor
   * @return void
   */
  public function __construct()
  {
    $this->setSong(new Song());
  }

  /**
   * Sets an error message
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
   * @param Song $song
   * @return void
   */
  public function setSong(Song $song)
  {
    $this->song = $song;
  }

  /**
   * Gets the list of instruments
   * @return array
   */
  public function getInstruments()
  {
    if(!($count = $this->countChannels()))
    {
      return array();
    }

    $instruments = array();

    for($i=0; $i<$count; $i++)
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
   * Overloads with $song methods
   * 
   * @param string $name method
   * @param array $arguments
   */
  public function __call($name, $arguments)
  {
    if(!method_exists($this->song, $name))
    {
      $message = sprintf(_('Song has no method called "%s"'), $name);

      trigger_error($message, E_USER_ERROR);
    }

    switch(count($arguments))
    {
      case 0: return $this->song->$name();
      case 1: return $this->song->$name($arguments[0]);
      default:
        $message = sprintf(_('%s method does not support %d arguments')
            , __METHOD__, count($arguments));

        trigger_error($message, E_USER_ERROR);
        break;
    }
  }
}
