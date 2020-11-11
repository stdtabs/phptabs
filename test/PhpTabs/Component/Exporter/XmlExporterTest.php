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
 * Tests XMLrepresentation with simple tablatures
 */
class XmlExporterTest extends TestCase
{
    public function getSimpleFiles()
    {
        $files = glob(PHPTABS_TEST_BASEDIR . '/samples/testS*');

        $filenames = [];

        foreach ($files as $filename) {
            $filenames[] = [
                $filename,
                str_replace('samples', 'files/xml', $filename) . '.xml'
            ];
        }

        return $filenames;
    }

    /**
     * Test XML
     *
     * @dataProvider getSimpleFiles
     */
    public function testXmlExporter($source, $result)
    {
        $tabs     = new PhpTabs($source);
        $text     = $tabs->toXml();
        $expected = file_get_contents($result);
        $this->assertEquals(
            $expected,
            $text,
            "File '$source' has not been exported with success"
        );
    }
}
