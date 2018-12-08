<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

# Defines base dir for tests
defined('PHPTABS_TEST_BASEDIR')   || define('PHPTABS_TEST_BASEDIR', realpath(__DIR__) . '/PhpTabs');

# Composer autoloader
include_once dirname(__DIR__) . '/vendor/autoload.php';

# Project-specific autoloader (Included for tests)
include_once dirname(__DIR__) . '/src/PhpTabs/bootstrap.php';

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase')
    && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
