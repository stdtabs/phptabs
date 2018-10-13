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

class Renderer
{
  /** 
   * @var \PhpTabs\Component\Tablature
   */
  private $tablature;

  /** 
   * @var \PhpTabs\Component\Renderer\RendererInterface
   */
  private $bridge;

  /**
   * List of supported types of renders 
   *
   * @var array
   */
  private $formats = array(
    'vextab'  => 'PhpTabs\\Renderer\\VexTab\\VexTabRenderer',
    'ascii'   => 'PhpTabs\\Renderer\\Ascii\\AsciiRenderer',
  );

  /**
   * Instanciates tablature container
   * 
   * @param  \PhpTabs\Component\Tablature $tablature
   */
  public function __construct(Tablature $tablature)
  {
    $this->tablature = $tablature;
  }

  /**
   * Set renderer format
   * 
   * @param  string $format
   * @return \PhpTabs\Component\Renderer\RendererInterface
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
