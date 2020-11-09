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
    public function testNotAllowedFormatException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->save('xxx');
    }

    public function testEmptySongDefaultException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->save();
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
     * Trying to write a non writable directory
     */
    public function testNonWritableDirectoryException()
    {
        $this->expectException(Exception::class);

        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        (new Writer($song->getTablature()))->save('/tabs.gp3');
    }

    /**
     * Trying to write a non writable file
     */
    public function testNonWritableFileException()
    {
        $this->expectException(Exception::class);

        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        (new Writer($song->getTablature()))->save(PHPTABS_TEST_BASEDIR . '/samples/nonWritableFile.gp5');
    }

    /**
     * Trying to build an undefined format
     */
    public function testUndefinedFormatException()
    {
        $this->expectException(Exception::class);

        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        (new Writer($song->getTablature()))->build('gp42');
    }

    /**
     * Let's record a file
     */
    public function testRecordFileOk()
    {
        $song = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/testSimpleTab.gp3');

        $writer = new Writer($song->getTablature());

        // Writing to disk
        $writer->save(PHPTABS_TEST_BASEDIR . '/samples/newFile.gp5');

        $this->assertFileExists(PHPTABS_TEST_BASEDIR . '/samples/newFile.gp5');
        unlink(PHPTABS_TEST_BASEDIR . '/samples/newFile.gp5');
    }
}
