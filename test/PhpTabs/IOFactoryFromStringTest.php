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
use PhpTabs\Component\FileInput;

/**
 * Tests IOFactory::fromString()
 */
class IOFactoryFromStringTest extends TestCase
{
    /**
     * Provide all source files
     */
    public function getAllSampleTabs()
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
            $serializedFilename = str_replace(
                '/samples/',
                '/files/serialized/',
                $filename
            ) . '.ser';
            //$filenames[] = [$filename, $jsonFilename, 'json'];
            //$filenames[] = [$filename, $serializedFilename, 'ser'];
            $filenames[] = [$filename];
        }

        return $filenames;
    }

    /**
     * Test simple tabs bijection WITHOUT type parameter
     *
     * @dataProvider getAllSampleTabs()
     */
    public function testSimpleTabsBijection($filename)
    {
        $content = file_get_contents($filename);
        $file = new FileInput($filename);
        $expected = IOFactory::fromFile($filename)->toArray();
        $import   = IOFactory::fromString($content, $file->getExtension());

        $this->assertEquals(
            $expected,
            $import->toArray(),
            "Simple tabs '$filename' fromString() fails"
        );
    }
}
