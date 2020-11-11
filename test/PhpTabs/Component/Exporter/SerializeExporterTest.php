<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component\Exporter;

use PHPUnit\Framework\TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests PHP serialized representation with simple tablatures
 */
class SerializeExporterTest extends TestCase
{
    public function getSimpleFiles()
    {
        $files = glob(PHPTABS_TEST_BASEDIR . '/samples/testS*');

        $filenames = [];

        foreach ($files as $filename) {
            $filenames[] = [
                $filename,
                str_replace('samples', 'files/serialized', $filename) . '.ser'
            ];
        }

        return $filenames;
    }

    /**
     * Test PHP serialized
     *
     * @dataProvider getSimpleFiles
     */
    public function testSerializedExporter($source, $result)
    {
        $tabs     = new PhpTabs($source);
        $text     = $tabs->toSerialized();
        $expected = file_get_contents($result);
        $this->assertEquals(
            unserialize($expected),
            unserialize($text),
            "File '$source' has not been exported with success"
        );
    }
}
