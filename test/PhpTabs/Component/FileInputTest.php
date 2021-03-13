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
use PhpTabs\Component\FileInput;

class FileInputTest extends TestCase
{
    protected function setUp() : void
    {
        $this->filename = PHPTABS_TEST_BASEDIR . '/samples/testNotAllowedExtension.xxx';
    }

    /**
     * Tests parts of path for File component
     */
    public function testPathParts()
    {
        $file = new FileInput($this->filename);

        $this->assertEquals($this->filename, $file->getPath());
        $this->assertEquals(dirname($this->filename), $file->getDirname());
        $this->assertEquals(basename($this->filename), $file->getBasename());
    }

    public function testStreamMethods()
    {
        // Reads stream position when stream has been closed
        $file = new FileInput($this->filename);
        $file->getInputStream()->closeStream();

        $this->assertEquals(false, $file->getInputStream()->getStreamPosition());

        // Reads stream read with offset
        $file = new FileInput($this->filename);
        $this->assertEquals('IER G', $file->getInputStream()->getStream(5, 5));
    }

    /**
     * Pointer exception
     */
    public function testPointerException()
    {
        $this->expectException(Exception::class);

        $file = new FileInput($this->filename);
        $file->getInputStream()->getStream(1);
        $file->getInputStream()->getStream(50000000);
    }

    /**
     * File does not exists exception
     */
    public function testFileDoesNotExistException()
    {
        $this->expectException(Exception::class);

        $file = new FileInput($this->filename . 't');
    }

    /**
     * Given path is a folder exception
     */
    public function testGivenPathIsFolderException()
    {
        $this->expectException(Exception::class);

        $file = new FileInput('/docs');
    }
}
