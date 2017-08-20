<?php

namespace PhpTabs\Component;

use Exception;

class Renderer
{
  /** 
   * @var \PhpTabs\Component\Tablature
   */
  private $tablature;

  /** 
   * @var \PhpTabs\Component\RendererInterface
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
   * @param  \PhpTabs\Component\Tablature $tablature
   */
  public function __construct(Tablature $tablature = null)
  {
    $this->tablature = $tablature;
  }

  /**
   * Set renderer format
   * 
   * @param  string $format
   * @return \PhpTabs\Component\RendererInterface
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
}
