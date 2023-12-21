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

class JsonWriterTest extends TestCase
{
    public static function getWriterScenario() : array
    {
        return [
            'midi->json' => ['testSimpleTab.mid'],
            'gp3->json'  => ['testSimpleTab.gp3'],
            'gp4->json'  => ['testSimpleTab.gp4'],
            'gp5->json'  => ['testSimpleTab.gp5'],
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
        // Remove JSON_PRETTY_PRINT
        $targetJsonContent = file_get_contents(PHPTABS_TEST_BASEDIR . '/files/json/' . $orgFilename . '.json');
        $json = json_encode(json_decode($targetJsonContent, true));
        // Converts to original format gives a specific JSON
        $this->assertEquals(
            $json,
            $song->convert('json'),
            'Default build content should be the same as file content'
        );
    }
}
