<?php

namespace PhpTabs\Renderer\VexTab;

use PhpTabs\Component\RendererInterface;

abstract class VexTabRendererHelper implements RendererInterface
{
  /**
   * Get an option
   * 
   * @param  string     $name
   * @param  int|string $default
   * @return int|string
   * @api
   * @since 0.5.0
   */
  public function getOption($name, $default)
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
   * @return \PhpTabs\Model\RendererInterface
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
   * @return \PhpTabs\Model\RendererInterface
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
