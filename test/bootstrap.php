<?php

# Defines base dir for tests
defined('PHPTABS_TEST_BASEDIR') || define('PHPTABS_TEST_BASEDIR', realpath(__DIR__) . '/PhpTabs');

# Composer autoloader
include_once dirname(__DIR__) . '/vendor/autoload.php';

# Project-specific autoloader (Included for tests)
include_once dirname(__DIR__) . '/src/PhpTabs/bootstrap.php';
