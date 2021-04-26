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

namespace PhpTabs\Component\Importer;

use Exception;

abstract class ParserBase
{
    protected $item;

    /**
     * Check that a key is set in a data array
     *
     * @throws \Exception if a key is not defined
     */
    protected function checkKeys(array $data, array $keys): void
    {
        $this->hasKeys($data, $keys);
    }

    /**
     * Require that a key must be set
     *
     * @throws \Exception if key is not set
     */
    private function hasKeys(array $data, array $keys): void
    {
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $data)) {
                throw new Exception("Invalid data: '{$key}' key must be set");
            }
        }
    }

    /**
     * Get parse result
     * 
     * @return mixed
     */
    public function parse()
    {
        return $this->item;
    }

    /**
     * Extends parser methods
     * 
     * @param  string $name      A method name
     * @param  array  $arguments Some arguments for the method
     * @return mixed
     */
    public function __call(string $name, array $arguments = [])
    {
        $parserName =
            __NAMESPACE__
            . '\\'
            . str_replace('parse', '', $name)
            . 'Parser';

        return (new $parserName(...$arguments))->parse();
    }
}
