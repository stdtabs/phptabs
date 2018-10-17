<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Renderer;

abstract class RendererHelper implements RendererInterface
{
  /**
   * @var array
   */
  private $options = [];

  /**
   * Get an option
   * 
   * @param  string     $name
   * @param  int|string $default
   * @return null|int|string
   * @api
   * @since 0.5.0
   */
  public function getOption($name, $default = null)
  {
    return isset($this->options[$name])
      ? $this->options[$name]
      : $default;
  }

  /**
   * Set an option
   * 
   * @param  string     $name
   * @param  int|string $value
   * @return \PhpTabs\Component\Renderer\RendererInterface
   * @api
   * @since 0.5.0
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Set all options
   * 
   * @param  array $options
   * @return \PhpTabs\Component\Renderer\RendererInterface
   * @api
   * @since 0.5.0
   */
  public function setOptions(array $options)
  {
    foreach ($options as $name => $value) {
      $this->setOption($name, $value);
    }

    return $this;
  }

  /**
   * Get all options
   * 
   * @return array
   * @api
   * @since 0.5.0
   */
  public function getOptions()
  {
    return $this->options;
  }
}
