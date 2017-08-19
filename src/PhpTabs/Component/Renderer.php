<?php

namespace PhpTabs\Component;

use Exception;

class Renderer
{
  /** 
   * @var PhpTabs\Component\Tablature
   */
  private $tablature;

  /** 
   * @var PhpTabs\Component\RendererInterface
   */
  private $bridge;

  /**
   * List of supported renders 
   *
   * @var array
   */
  private $formats = array(
    'vextab'   => 'PhpTabs\\Renderer\\VexTab\\VexTabRenderer',
  );

  /**
   * Instanciates tablature container
   * 
   * @param  PhpTabs\Component\Tablature $tablature
   */
  public function __construct(Tablature $tablature = null)
  {
    $this->tablature = $tablature;
  }

  /**
   * Set renderer format
   * 
   * @param  string $format
   */
  public function setFormat($format = null)
  {
    if (!isset($this->formats[$format])) {

      $message = sprintf(
        'Output format "%s" is not supported',
        $format
      );

      throw new Exception($message);
    }

    $this->bridge = new $this->formats[$format]($this->tablature->getSong());

    return $this->bridge;
  }

  /**
   * Overloads with $bridge methods
   * 
   * @param  string $name method
   * @param  array  $arguments
   * @return mixed
   */
  public function __call($name, $arguments)
  {
    if (null === $this->bridge) {
      throw new Exception('Bridge must be set.');
    }

    if (!method_exists($this->bridge, $name)) {

      $message = sprintf(
        'Renderer has no method called "%s"',
        $name
      );

      trigger_error($message, E_USER_ERROR);
    }

    switch (count($arguments))
    {
      case 0: return $this->bridge->$name();
      case 1: return $this->bridge->$name($arguments[0]);
      case 2: return $this->bridge->$name($arguments[0], $arguments[1]);
      default:
        $message = sprintf('%s method does not support %d arguments',
            __METHOD__,
            count($arguments)
        );

        trigger_error($message, E_USER_ERROR);
    }
  }
}
