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

class Factory
{
    private $reader;

    public function __construct(GuitarProReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return mixed
     */
    public function get(string $name, string $parserName)
    {
        $name = __NAMESPACE__ . '\\' . $name;

        $object = new $name();

        $object->setReader($this->reader);
        $object->setParserName($parserName);

        return $object;
    }
}
