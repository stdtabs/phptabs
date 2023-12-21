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

class SerializedWriterTest extends TestCase
{
    public static function getWriterScenario() : array
    {
        return [
            'midi->ser' => ['testSimpleTab.mid'],
            'gp3->ser'  => ['testSimpleTab.gp3'],
            'gp4->ser'  => ['testSimpleTab.gp4'],
            'gp5->ser'  => ['testSimpleTab.gp5'],
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

        $serialized = file_get_contents(PHPTABS_TEST_BASEDIR . '/files/serialized/' . $orgFilename . '.ser');

        // Converts to original format gives a specific serialized content
        $this->assertEquals(
            unserialize($serialized),
            unserialize($song->convert('ser')),
            'Default build content should be the same as file content'
        );
    }
}
