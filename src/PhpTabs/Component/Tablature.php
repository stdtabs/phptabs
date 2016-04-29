<?php

namespace PhpTabs\Component;

use PhpTabs\Model\Song;

class Tablature
{ 
  /**
   * @var string Error message
   */
  private $error = '';

  /**
   * @var object Song
   */
  private $song = '';


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
   * @return string Error set during file read operations
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
    return !($this->error == ''); // @todo Should merge all errors
  }


  public function setSong(Song $song)
  {
    $this->song = $song;
  }


  /**
   * Overloads with $song methods
   * 
   * @param string $name method name
   * @param array $arguments arguments to pass
   */
  public function __call($name, $arguments)
  {
    # Method is not defined
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
