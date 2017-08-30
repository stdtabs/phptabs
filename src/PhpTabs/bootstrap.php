<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs;

use PhpTabs\Component\Autoloader;

/**
 * Loads the project specific autoloader
 *
 * Include this file only if you do NOT use composer
 */
require_once __DIR__ . '/Component/Autoloader.php';

Autoloader::register();
