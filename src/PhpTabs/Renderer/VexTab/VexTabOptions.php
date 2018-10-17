<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Renderer\VexTab;

use PhpTabs\Component\Renderer\RendererInterface;

class VexTabOptions
{
  /**
   * Global options
   * 
   * @var array
   */
  private $globals = [];

  /**
   * Stave options
   * 
   * @var array
   */
  private $stave = [];

  /**
   * Default options
   * 
   * @var array
   */
  private $options = [
    'notation'           => 'true',
    'tablature'          => 'true',
    'measures_per_stave' => 1,
    'scale'              => 1,
    'space'              => 16,
    'width'              => 520,
    'tab-stems'          => 'false',
    'tab-stem-direction' => 'auto'
  ];

  /**
   * Allowed global values
   * 
   * @var array
   */
  private $defGlobals = [
    'space'               => 'is_int',
    'scale'               => 'is_float',
    'stave-distance'      => 'is_int',
    'width'               => 'is_int',

    'font-size'           => 'is_int',
    'font-face'           => 'is_string',
    'font-style'          => 'is_string',

    'tab-stems'           => ['false', 'true'],
    'tab-stem-direction'  => ['up', 'down'],
    'tempo'               => 'is_int',
    'player'              => ['false', 'true']
  ];

  /**
   * Allowed stave values
   * 
   * @var array
   */
  private $defStave = [
    'notation'            => ['false', 'true'],
    'tablature'           => ['true', 'false'],
    'clef'                => ['treble', 'alto', 'tenor', 'bass', 'percussion'],
  ];

  /**
   * @var \PhpTabs\Component\Renderer\RendererInterface
   */
  protected $renderer;

  /**
   * Constructor
   * Parse options scopes (global, tabstave)
   * 
   * @param \PhpTabs\Component\Renderer\RendererInterface $renderer
   */
  public function __construct(RendererInterface $renderer)
  {
    $this->renderer = $renderer;

    // Defaults
    foreach ($this->options as $name => $value) {
      $this->parseOption($name, $value);
    }

    foreach ($renderer->getOptions() as $name => $value) {
      $this->parseOption($name, $value);
    }
  }

  /**
   * Render all options for a given scope
   * 
   * @param  string $scope globals or stave
   * @return string
   * @api
   * @since 0.5.0
   */
  public function render($scope)
  {
    $options = [];

    switch ($scope) {
      case 'globals':
        $options = $this->globals;
        break;
      case 'stave':
        $options = $this->stave;
        break;
    }

    $text = '';
    foreach ($options as $key => $value) {
      $text .= sprintf(' %s=%s', $key, $value);
    }

    return $text;
  }

  /**
   * Add a new option
   * 
   * @param string     $name
   * @param string|int $value
   */
  public function add($name, $value)
  {
    $this->parseOption($name, $value);
  }

  /**
   * Parse options scope
   * 
   * @param string     $name
   * @param string|int|bool $value
   */
  private function parseOption($name, $value)
  {
    if ($value === true) {
      $value = 'true';
    }

    if ($value === false) {
      $value = 'false';
    }

    // global
    if (isset($this->defGlobals[$name])) {
      if ($this->validateOption($value, $this->defGlobals[$name])) {
        $this->globals[$name] = $value;
      }
    }

    // stave
    if (isset($this->defStave[$name])) {
      if ($this->validateOption($value, $this->defStave[$name])) {
        $this->stave[$name] = $value;
      }
    }
  }

  /**
   * Validate given option value
   * 
   * @param  string|int|float $value
   * @param  string|array $definition
   * @return bool
   */
  private function validateOption($value, $definition)
  {
    switch ($definition) {
      case 'is_int':
        return is_int($value);
      case 'is_float':
        return is_float($value) || is_int($value);
      case 'is_string':
        return is_string($value);
    }

    if (is_array($definition)) {
      return in_array($value, $definition) && $value !== $definition[0];
    }
  }
}
