<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest;

use Exception;
use PHPUnit\Framework\TestCase;
use PhpTabs\IOFactory;

/**
 * Tests IOFactory::fromSerialized()
 */
class IOFactoryFromSerializedTest extends TestCase
{
    /**
     * A provider for various scenarios that throw \Exception
     */
    public function getExceptionScenarios()
    {
        return [
        [1.25],   // Float as data
        ];
    }

    /**
     * @dataProvider      getExceptionScenarios
     */
    public function testExceptionScenario($data)
    {
        $this->expectException(Exception::class);

        IOFactory::fromSerialized($data);
    }
}
