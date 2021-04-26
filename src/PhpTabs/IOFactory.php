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

namespace PhpTabs;

use Exception;
use PhpTabs\Component\FileInput;

abstract class IOFactory
{
    /**
     * Create a PhpTabs instance
     */
    public static function create(?string $pathname = null): PhpTabs
    {
        return new PhpTabs($pathname);
    }

    /**
     * Create a PhpTabs instance from an array
     */
    public static function fromArray(array $data): PhpTabs
    {
        return self::create()->fromArray($data);
    }

    /**
     * Load data from a file
     *
     * @param  string $pathname A complete pathname
     * @param  string $type     Force a file type read
     */
    public static function fromFile(string $pathname, ?string $type = null): PhpTabs
    {
        self::checkFile($pathname);

        $type = is_null($type)
            ? pathinfo($pathname, PATHINFO_EXTENSION)
            : $type;

        $file = new FileInput($pathname);

        // Force $type parser
        return self::fromString(
            $file->getInputStream()
                 ->getStream(
                    $file->getInputStream()->getSize()
                ),
            $type
        );
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

        return self::create($filename);
    }

    /**
     * Import a tablature from a JSON file
     *
     * @throws \Exception if JSON decode failed
     */
    public static function fromJsonFile(string $filename): PhpTabs
    {
        self::checkFile($filename);

        return self::create($filename);
    }

    /**
     * Import a tabs from a PHP serialized string
     */
    public static function fromSerialized(string $data): PhpTabs
    {
        return self::fromString($data, 'ser');
    }

    /**
     * Import a tabs from a PHP serialized string
     */
    public static function fromJson(string $data): PhpTabs
    {
        return self::fromString($data, 'json');
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
        if (! is_readable($filename)) {
            throw new Exception(
                "FILE_ERROR Filename '{$filename}' is not readable"
            );
        }

        // Must be a file
        if (! is_file($filename)) {
            throw new Exception(
                "FILE_ERROR Filename '{$filename}' must be a file"
            );
        }
    }
}
