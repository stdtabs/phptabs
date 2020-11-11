<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Writer;

use PHPUnit\Framework\TestCase;
use PhpTabs\PhpTabs;

class YamlWriterTest extends TestCase
{
    public function getWriterScenario() : array
    {
        return [
            'midi->yml' => ['testSimpleTab.mid'],
            'gp3->yml'  => ['testSimpleTab.gp3'],
            'gp4->yml'  => ['testSimpleTab.gp4'],
            'gp5->yml'  => ['testSimpleTab.gp5'],
        ];
    }

    /**
     * Test that convert is valid
     *
     * @dataProvider getWriterScenario
     */
    public function testConvert($orgFilename)
    {
        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/' . $orgFilename);
        $expectedXml = file_get_contents(PHPTABS_TEST_BASEDIR . '/files/yaml/' . $orgFilename . '.yml');
        
        // Converts to original format gives a specific JSON
        $this->assertEquals(
            $expectedXml,
            $song->convert('yml'),
            'Default build content should be the same as file content'
        );
    }
}
