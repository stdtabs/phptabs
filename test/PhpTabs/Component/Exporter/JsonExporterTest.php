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
 * Tests JSON export with simple tablatures
 */
class JsonExporterTest extends TestCase
{
    public static function getSimpleFiles()
    {
        $files = glob(PHPTABS_TEST_BASEDIR . '/samples/testS*');

        $filenames = [];

        foreach ($files as $filename) {
            $filenames[] = [
                $filename,
                str_replace('samples', 'files/json', $filename) . '.json'
            ];
        }

        return $filenames;
    }

    /**
     * JSON exports
     *
     * @dataProvider getSimpleFiles
     */
    public function testJsonExporter($source, $result)
    {
        $tabs     = new PhpTabs($source);
        $text     = $tabs->toJson(JSON_PRETTY_PRINT);
        $expected = file_get_contents($result);
        $this->assertEquals(
            $expected,
            $text,
            "File '$source' has not been exported with success"
        );
    }

    /**
     * Empty tabs JSON exports
     */
    public function testEmptyTabsJsonExporter()
    {
        $tabs     = new PhpTabs();
        $text     = $tabs->toJson();
        $expected = file_get_contents(PHPTABS_TEST_BASEDIR . '/files/json/empty-tabs.json');
        $this->assertEquals(
            $expected,
            $text,
            "Empty tabs has not been exported with success"
        );
    }
}
