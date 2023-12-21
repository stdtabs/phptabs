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
 * Tests IOFactory::fromSerializedFile()
 */
class IOFactoryFromSerializedFileTest extends TestCase
{
    /**
     * A provider for various scenarios that throw \Exception
     */
    public static function getExceptionScenarios()
    {
        return [
            [1.25],   // Float as filename
            [PHPTABS_TEST_BASEDIR . '/sample'],   // Unreadable filename
            [PHPTABS_TEST_BASEDIR . '/samples/'], // Dir as filename
        ];
    }

    /**
     * @dataProvider      getExceptionScenarios
     */
    public function testExceptionScenario($filename)
    {
        $this->expectException(Exception::class);

        IOFactory::fromSerializedFile($filename);
    }

    /**
     * Provide all serialized & source files
     */
    public static function getAllSampleTabs()
    {
        $files = glob(
            PHPTABS_TEST_BASEDIR
            . '/samples/testS*'
        );

        $filenames = [];

        foreach ($files as $filename) {
            $serFilename = str_replace(
                '/samples/',
                '/files/serialized/',
                $filename
            ) . '.ser';
            $filenames[] = [$filename, $serFilename];
        }

        return $filenames;
    }

    /**
     * Test simple tabs bijection
     *
     * @dataProvider getAllSampleTabs()
     */
    public function testSimpleTabsBijection($filename, $serFilename)
    {
        $tabs     = IOFactory::fromFile($filename);
        $expected = $tabs->toArray();
        $import   = IOFactory::fromSerializedFile($serFilename);

        $this->assertEquals(
            $expected,
            $import->toArray(),
            "Simple tabs '$filename' fromSerializedFile() fails"
        );
    }
}
