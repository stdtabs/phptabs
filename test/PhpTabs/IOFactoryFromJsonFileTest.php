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
 * Tests IOFactory::fromJsonFile()
 */
class IOFactoryFromJsonFileTest extends TestCase
{
    /**
     * A provider for various scenarios that throw \Exception
     */
    public static function getExceptionScenarios()
    {
        return [
            [1.25],   // Float as filename
            [PHPTABS_TEST_BASEDIR . '/sample'],   // Unreadable filename
            [PHPTABS_TEST_BASEDIR . '/samples/'],  // Dir as filename
        ];
    }

    /**
     * @dataProvider      getExceptionScenarios
     */
    public function testExceptionScenario($filename)
    {
        $this->expectException(Exception::class);

        IOFactory::fromJsonFile($filename);
    }

    /**
     * Provide all JSON & source files
     */
    public static function getAllSampleTabs()
    {
        $files = glob(
            PHPTABS_TEST_BASEDIR
            . '/samples/testS*'
        );

        $filenames = [];

        foreach ($files as $filename) {
            $jsonFilename = str_replace(
                '/samples/',
                '/files/json/',
                $filename
            ) . '.json';
            $filenames[] = [$filename, $jsonFilename];
        }

        return $filenames;
    }

    /**
     * Test simple tabs bijection
     *
     * @dataProvider getAllSampleTabs()
     */
    public function testSimpleTabsBijection($filename, $jsonFilename)
    {
        $tabs     = IOFactory::fromFile($filename);
        $expected = $tabs->toArray();
        $import   = IOFactory::fromJsonFile($jsonFilename);

        $this->assertEquals(
            $expected,
            $import->toArray(),
            "Simple tabs '$filename' fromJsonFile() fails"
        );
    }
}
