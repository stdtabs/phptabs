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

class GuitarPro3WriterTest extends TestCase
{
    protected function setUp() : void
    {
        $this->path = '/samples/testSimpleTab.gp3';
        $this->tablature = new PhpTabs(PHPTABS_TEST_BASEDIR . $this->path);
    }

    /**
     * Convert method
     */
    public function testConvert()
    {
        // Converts to default format (not specified)
        $this->assertEquals(
            file_get_contents(PHPTABS_TEST_BASEDIR . $this->path),
            $this->tablature->convert(),
            'Default build content should be the same as file content'
        );

        // Converts to gp3 format
        $this->assertEquals(
            file_get_contents(PHPTABS_TEST_BASEDIR . $this->path),
            $this->tablature->toGuitarPro3(),
            'GP3 build content should be the same as file content'
        );
    }

    protected function tearDown() : void
    {
        unset($this->tablature);
    }
}
