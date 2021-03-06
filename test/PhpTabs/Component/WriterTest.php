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
use PhpTabs\Component\Tablature;
use PhpTabs\Component\Writer;
use PhpTabs\PhpTabs;

class WriterTest extends TestCase
{
    public function testNotAllowedFilenameFormatException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->save('xxx');
    }

    public function testNotAllowedConvertFormatException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('xxx');
    }

    /**
     * gp3
     */
    public function testEmptySongGp3Exception()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('gp3');
    }

    /**
     * gp4
     */
    public function testEmptySongGp4Exception()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('gp4');
    }

    /**
     * gp5
     */
    public function testEmptySongGp5Exception()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('gp5');
    }

    /**
     * MIDI
     */
    public function testEmptySongMidiException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('mid');
    }

    /**
     * JSON
     */
    public function testEmptySongJsonException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('json');
    }

    /**
     * PHP serialized
     */
    public function testEmptySongSerializedException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('ser');
    }

    /**
     * TEXT
     */
    public function testEmptySongTxtException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('txt');
    }

    /**
     * XML
     */
    public function testEmptySongXmlException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('xml');
    }

    /**
     * YAML
     */
    public function testEmptySongYamlException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('yml');
    }

    /**
     * Trying to write a non writable directory
     */
    public function testNonWritableDirectoryException()
    {
        $this->expectException(Exception::class);

        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        $song->save('/tabs.gp3');
    }

    /**
     * Trying to write a non writable file
     *
     * @todo rewrite this test to make it working on CI
     * 
    public function testNonWritableFileException()
    {
        $this->expectException(Exception::class);

        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        $song->writer()->save(PHPTABS_TEST_BASEDIR . '/samples/nonWritableFile.gp5');
    }
    */

    /**
     * Trying to build an undefined format
     */
    public function testUndefinedFormatException()
    {
        $this->expectException(Exception::class);

        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        $song->build('gp42');
    }

    /**
     * Let's record a file
     */
    public function testRecordFileOk()
    {
        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        // Writing to disk
        $song->save(PHPTABS_TEST_BASEDIR . '/samples/newFile.gp5');

        $this->assertFileExists(PHPTABS_TEST_BASEDIR . '/samples/newFile.gp5');
        unlink(PHPTABS_TEST_BASEDIR . '/samples/newFile.gp5');
    }

    /**
     * Let's record a file with a non-standard extension
     */
    public function testRecordFileWithNonStandardExtensionOk()
    {
        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        // Writing to disk
        $song->save(PHPTABS_TEST_BASEDIR . '/samples/newFile.gpp', 'gp5');

        $this->assertFileExists(PHPTABS_TEST_BASEDIR . '/samples/newFile.gpp');

        // File has been converted to Guitar Pro 5
        $this->assertSame(
            file_get_contents(PHPTABS_TEST_BASEDIR . '/samples/newFile.gpp'),
            $song->convert('gp5')
        );        
        unlink(PHPTABS_TEST_BASEDIR . '/samples/newFile.gpp');
    }
}
