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
 * Tests IOFactory::fromJson()
 */
class IOFactoryFromJsonTest extends TestCase
{
    /**
     * A provider for various scenarios that throw \Exception
     */
    public static function getExceptionScenarios()
    {
        return [
        [PHPTABS_TEST_BASEDIR . '/sample'],   // Unreadable filename
        [PHPTABS_TEST_BASEDIR . '/samples/'],  // Dir as filename
        [PHPTABS_TEST_BASEDIR . '/samples/testSimpleMidi.mid']  // Not a valid JSON file
        ];
    }

    /**
     * @dataProvider      getExceptionScenarios
     */
    public function testExceptionScenario($data)
    {
        $this->expectException(Exception::class);

        IOFactory::fromJson($data);
    }
}
