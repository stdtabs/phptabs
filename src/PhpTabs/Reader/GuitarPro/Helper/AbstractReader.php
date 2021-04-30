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

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface;

abstract class AbstractReader
{
    /**
     * @var GuitarProReaderInterface
     */
    protected $reader;

    /**
     * @var string
     */
    protected $parserName;

    public function setReader(GuitarProReaderInterface $reader): void
    {
        $this->reader = $reader;
    }

    public function setParserName(string $parserName): void
    {
        $this->parserName = $parserName;
    }

    public function getParserName(): string
    {
        return $this->parserName;
    }
}
