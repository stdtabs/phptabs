<?php

namespace PhpTabs\Component;

interface RendererInterface
{
  /**
   * @param  int $index Track index
   * @return string
   */
  public function render($index);

  /**
   * @param  string     $name
   * @param  int|string $value
   * @return \PhpTabs\Model\RendererInterface
   */
  public function setOption($name, $value);

  /**
   * @param  string     $name
   * @param  int|string $default
   * @return int|string
   */
  public function getOption($name, $default);

  /**
   * @param  array $options
   * @return \PhpTabs\Model\RendererInterface
   */
  public function setOptions(array $options);

  /**
   * @return array
   */
  public function getOptions();
}
