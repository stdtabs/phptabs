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

/*
 * MIDI writer still has some problems with complex timings.
 * This test is a temporary and very small test
 *
 * @todo Depreciate files and improve this test when the writer will
 * be OK
 */
class MidiWriterTest extends TestCase
{
    protected function setUp() : void
    {
        $this->path = '/files/midi/minimal.gp5';
        $this->midi = '/files/midi/minimal.mid';

        $this->tablature = new PhpTabs(PHPTABS_TEST_BASEDIR . $this->path);
    }

    /**
     * Convert method
     */
    public function testConvertMidi()
    {
        // Converts to default format (not specified)
        $this->assertEquals(
            file_get_contents(PHPTABS_TEST_BASEDIR . $this->midi),
            $this->tablature->toMidi(),
            'Default build content should be the same as file content'
        );
    }

    protected function tearDown() : void
    {
        unset($this->tablature);
    }
}
