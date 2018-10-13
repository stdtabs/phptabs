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
   * @return \PhpTabs\Component\Renderer\RendererInterface
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
   * @return \PhpTabs\Component\Renderer\RendererInterface
   */
  public function setOptions(array $options);

  /**
   * @return array
   */
  public function getOptions();
}
