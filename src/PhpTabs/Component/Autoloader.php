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

/**
 * Project-specific autoloader
 */
abstract class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload'], true, true);
    }

    public static function autoload(string $class): void
    {
        $prefix = 'PhpTabs\\';

        $baseDir = dirname(__DIR__) . '/';

        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relativeClass = substr($class, $len);

        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            include $file;
        }
    }
}
