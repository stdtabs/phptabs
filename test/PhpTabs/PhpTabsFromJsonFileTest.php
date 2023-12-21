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

use PHPUnit\Framework\TestCase;
use PhpTabs\IOFactory;
use PhpTabs\PhpTabs;

/**
 * Tests new PhpTabs('filename.json')
 */
class PhpTabsFromJsonFileTest extends TestCase
{
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
        $import   = new PhpTabs($jsonFilename);

        $this->assertEquals(
            $expected,
            $import->toArray(),
            "Simple tabs '$filename' fromJsonFile() fails"
        );
    }
}
