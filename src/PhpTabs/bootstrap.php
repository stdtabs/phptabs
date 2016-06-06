<?php

namespace PhpTabs;

use PhpTabs\Component\Autoloader;

/**
 * Loads the project specific autoloader
 *
 * Include this file only if you do NOT use composer
 */
include_once __DIR__ . '/Component/Autoloader.php';

Autoloader::register();
