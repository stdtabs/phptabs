<?php

declare(strict_types = 1);

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
     * Render a track stored at the given index
     */
    public function render(?int $index = null): string;

    /**
     * @param  string     $name
     * @param  int|string $value
     */
    public function setOption(string $name, $value): self;

    /**
     * @param  string     $name
     * @param  int|string $default
     * @return int|string
     */
    public function getOption(string $name, $default);

    public function setOptions(array $options): self;

    public function getOptions(): array;
}
