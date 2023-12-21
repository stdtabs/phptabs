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

use Exception;
use PHPUnit\Framework\TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests Importer component
 */
class ImporterTest extends TestCase
{
    protected function setUp() : void
    {
        $this->tablature = new PhpTabs(
            PHPTABS_TEST_BASEDIR
            . '/samples/testSimpleTab.gp5'
        );
    }

    /**
     * Test empty tabs bijection
     */
    public function testEmptyTabsBijection()
    {
        $tabs     = new PhpTabs();
        $expected = $tabs->toArray();
        $result   = $tabs->fromArray($tabs->toArray())->toArray();

        $this->assertEquals(
            $expected,
            $result,
            'Empty tabs export-import fails'
        );
    }

    /**
     * Provider
     */
    public static function getAllSampleTabs()
    {
        $files = glob(
            PHPTABS_TEST_BASEDIR
            . '/samples/testS*'
        );

        $filenames = [];

        foreach ($files as $filename) {
            $filenames[] = [$filename];
        }

        return $filenames;
    }

    /**
     * Test simple tabs bijection
     *
     * @dataProvider getAllSampleTabs()
     */
    public function testSimpleTabsBijection($filename)
    {
        $tabs     = new PhpTabs($filename);
        $expected = $tabs->toArray();
        $import   = (new PhpTabs())->fromArray($expected);

        $this->assertEquals(
            $expected,
            $import->toArray(),
            "Simple tabs '$filename' export-import fails"
        );
    }

    public function testImportException()
    {
        $this->expectException(Exception::class);

        // Not a valid import format
        (new PhpTabs())->fromArray([]);
    }
}
