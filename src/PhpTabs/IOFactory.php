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

namespace PhpTabs;

use Exception;

abstract class IOFactory
{
    /**
     * Create a PhpTabs instance
     */
    public static function create(string $pathname = null): PhpTabs
    {
        return new PhpTabs($pathname);
    }

    /**
     * Create a PhpTabs instance from an array
     */
    public static function fromArray(array $data): PhpTabs
    {
        return self::create()->import($data);
    }

    /**
     * Load data from a file
     *
     * @param  string $pathname A complete pathname
     * @param  string $type     Force a file type read
     */
    public static function fromFile(string $pathname, string $type = null): PhpTabs
    {
        self::checkFile($pathname);

        $type = is_null($type)
            ? pathinfo($pathname, PATHINFO_EXTENSION)
            : $type;

        switch (strtolower($type)) {
            case 'json':
                return self::fromJsonFile($pathname);
            case 'ser':
                return self::fromSerializedFile($pathname);
        }

        return self::create($pathname);
    }

    /**
     * Load and parse from a string
     */
    public static function fromString(string $content, string $type): PhpTabs
    {
        return self::create()
                   ->fromString($content, $type);
    }

    /**
     * Import a tablature from a PHP serialized file
     *
     * @throws \Exception if unserialize method failed
     */
    public static function fromSerializedFile(string $filename): PhpTabs
    {
        self::checkFile($filename);

        return self::fromSerialized(
            file_get_contents($filename)
        );
    }

    /**
     * Import a tablature from a JSON file
     *
     * @throws \Exception if JSON decode failed
     */
    public static function fromJsonFile(string $filename): PhpTabs
    {
        self::checkFile($filename);

        return self::fromJson(
            file_get_contents($filename)
        );
    }

    /**
     * Import a tabs from a PHP serialized string
     */
    public static function fromSerialized(string $data): PhpTabs
    {
        $data = @unserialize( // Skip warning
            $data,
            ['allowed_classes' => false]
        );

        // unserialize failed
        if ($data === false) {
            $message = sprintf('UNSERIALIZE_FAILURE');

            throw new Exception($message);
        }

        return self::fromArray($data);
    }

    /**
     * Import a tabs from a PHP serialized string
     */
    public static function fromJson(string $data): PhpTabs
    {
        $data = json_decode($data, true);

        // JSON decoding error
        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = sprintf(
                'JSON_DECODE_FAILURE: Error number %d - %s',
                json_last_error(),
                json_last_error_msg()
            );

            throw new Exception($message);
        }

        return self::fromArray($data);
    }

    /**
     * Check that given filename is a string and is readable
     *
     * @throws \Exception if filename is not a file
     *                 or if file is not readable
     */
    public static function checkFile(string $filename): void
    {
        // Must be readable
        if (!is_readable($filename)) {
            throw new Exception(
                "FILE_ERROR Filename '$filename' is not readable"
            );
        }

        // Must be a file
        if (!is_file($filename)) {
            throw new Exception(
                "FILE_ERROR Filename '$filename' must be a file"
            );
        }
    }
}
