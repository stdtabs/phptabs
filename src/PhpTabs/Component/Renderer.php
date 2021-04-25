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

namespace PhpTabs\Component;

use Exception;
use PhpTabs\Component\Renderer\RendererInterface;
use PhpTabs\Renderer\Ascii\AsciiRenderer;
use PhpTabs\Renderer\VexTab\VexTabRenderer;

final class Renderer
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
     * @var array<string,string>
     */
    private $formats = [
        'vextab'  => VexTabRenderer::class,
        'ascii'   => AsciiRenderer::class,
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
    public function setFormat(?string $format = null): RendererInterface
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
