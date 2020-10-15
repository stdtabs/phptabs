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

use PhpTabs\Component\Renderer\RendererInterface;
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
    private $formats = [
        'vextab'  => 'PhpTabs\\Renderer\\VexTab\\VexTabRenderer',
        'ascii'   => 'PhpTabs\\Renderer\\Ascii\\AsciiRenderer',
    ];

    /**
     * Instanciates tablature container
     */
    public function __construct(Tablature $tablature)
    {
        $this->tablature = $tablature;
    }

    /**
     * Set renderer format
     */
    public function setFormat(string $format = null): RendererInterface
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
