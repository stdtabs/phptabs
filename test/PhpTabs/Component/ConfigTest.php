<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component;

use PHPUnit\Framework\TestCase;
use PhpTabs\Component\Config;

/**
 * Tests Config component
 */
class ConfigTest extends TestCase
{
    public function testConfig()
    {
        Config::clear();

        // sets a good key
        Config::set('Sense', 42);
        $this->assertEquals(42, Config::get('Sense'));

        // Gets all configs
        $expected = [
            'Sense' => 42
        ];

        $this->assertEquals($expected, Config::getAll());
    }
}
