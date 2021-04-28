<?php

declare(strict_types=1);

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

final class VexTabOptions
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
    private $renderer;

    /**
     * Constructor
     * Parse options scopes (global, tabstave)
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
     * @api
     * @since  0.5.0
     */
    public function render(string $scope): string
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
     * @param string|int $value
     */
    public function add(string $name, $value): void
    {
        $this->parseOption($name, $value);
    }

    /**
     * Parse options scope
     *
     * @param string|int|bool $value
     */
    private function parseOption(string $name, $value): void
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
     * @param  string|array     $definition
     */
    private function validateOption($value, $definition): bool
    {
        switch ($definition) {
            case 'is_int':
                return is_int($value);
            case 'is_float':
                return is_float($value) || is_int($value);
            case 'is_string':
                return is_string($value);
        }

        return is_array($definition)
            ? in_array($value, $definition) && $value !== $definition[0]
            : false;
    }
}
