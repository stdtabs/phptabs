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

namespace PhpTabs\Component;

abstract class Config
{
    /**
     * @var array config options
     */
    private static $data = [];

    /**
     * Gets a defined option
     *
     * @param string $key     option name
     * @param mixed  $default optional return value if not defined
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return is_string($key) && isset(self::$data[$key])
            ? self::$data[$key]
            : $default;
    }

    /**
     * Sets an option
     *
     * @param string $key   option name
     * @param mixed  $value optional option value
     */
    public static function set(string $key, $value = null): void
    {
        if (is_scalar($key)) {
            self::$data[$key] = $value;
        }
    }

    /**
     * Gets all defined options
     */
    public static function getAll(): array
    {
        return self::$data;
    }

    /**
     * Delete all config values
     */
    public static function clear(): void
    {
        self::$data = [];
    }
}
