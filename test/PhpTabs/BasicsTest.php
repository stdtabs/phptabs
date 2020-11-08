<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest;

use Exception;
use PHPUnit\Framework\TestCase;
use PhpTabs\PhpTabs;

class BasicsTest extends TestCase
{
    /**
     * Tests write mode with empty attributes
     */
    public function testWriteModeWithEmptyAttributes()
    {
        $tablature = new PhpTabs();

        // Meta attributes
        $this->assertEquals('', $tablature->getName());
        $this->assertEquals('', $tablature->getArtist());
        $this->assertEquals('', $tablature->getAlbum());
        $this->assertEquals('', $tablature->getAuthor());
        $this->assertEquals('', $tablature->getCopyright());
        $this->assertEquals('', $tablature->getWriter());
        $this->assertEquals('', $tablature->getComments());
        $this->assertEquals('', $tablature->getDate());
        $this->assertEquals('', $tablature->getTranscriber());

        // Tracks
        $this->assertEquals(0, $tablature->countTracks());
        $this->assertEquals(array(), $tablature->getTracks());

        // Channels
        $this->assertEquals(0, $tablature->countChannels());
        $this->assertEquals(array(), $tablature->getChannels());

        // MeasureHeaders
        $this->assertEquals(0, $tablature->countMeasureHeaders());
        $this->assertEquals(array(), $tablature->getMeasureHeaders());

        // Instruments
        $this->assertEquals(0, $tablature->countInstruments());
        $this->assertEquals(array(), $tablature->getInstruments());
    
        $this->assertInstanceOf('PhpTabs\\Component\\Tablature', $tablature->getTablature());
    }

    /**
     * Tests read mode with a non readable file
     */
    public function testReadModeWithNonReadableFile()
    {
        $this->expectException(Exception::class);

        // Path not reachable
        $tablature = new PhpTabs('thisFileDoesNotExist.gp3');
    }

    public function testExceptionTrackNotDefined()
    {
        $this->expectException(Exception::class);

        $tablature = new PhpTabs();
    
        $tablature->getTrack(0);
    }

    public function testExceptionChannelNotDefined()
    {
        $this->expectException(Exception::class);

        $tablature = new PhpTabs();
    
        $tablature->getChannel(0);
    }

    public function testExceptionMeasureHeaderNotDefined()
    {
        $this->expectException(Exception::class);

        $tablature = new PhpTabs();
    
        $tablature->getMeasureHeader(0);
    }
}
