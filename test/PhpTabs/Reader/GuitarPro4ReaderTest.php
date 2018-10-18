<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Reader;

use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

class GuitarPro4ReaderTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->filename = 'testSimpleTab.gp4';
    $this->tablature = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/' . $this->filename);
  }

  /**
   * Tests read mode with a simple tablature
   * Guitar Pro 4
   */
  public function testReadModeWithSimpleGuitarPro4Tab()
  {
    # Errors
    $this->assertEquals(false, $this->tablature->hasError());
    $this->assertEquals(null, $this->tablature->getError());

    # Meta attributes
    $this->assertEquals('Testing name', $this->tablature->getName());
    $this->assertEquals('Testing artist', $this->tablature->getArtist());
    $this->assertEquals('Testing album', $this->tablature->getAlbum());
    $this->assertEquals('Testing author', $this->tablature->getAuthor());
    $this->assertEquals('Testing copyright', $this->tablature->getCopyright());
    $this->assertEquals('Testing writer', $this->tablature->getWriter());
    $this->assertEquals("Testing comments line 1\nTesting comments line 2"
      , $this->tablature->getComments());
    $this->assertEquals('', $this->tablature->getDate());       #Not supported by Guitar Pro 4
    $this->assertEquals('', $this->tablature->getTranscriber());#Not supported by Guitar Pro 4

    # Tracks
    $this->assertEquals(2, $this->tablature->countTracks());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Music\\Track', $this->tablature->getTracks());
    $this->assertInstanceOf('PhpTabs\\Music\\Track', $this->tablature->getTrack(0));

    # Channels
    $this->assertEquals(2, $this->tablature->countChannels());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Music\\Channel', $this->tablature->getChannels());
    $this->assertInstanceOf('PhpTabs\\Music\\Channel', $this->tablature->getChannel(0));

    # Instruments
    $this->assertEquals(2, $this->tablature->countInstruments());

    $expected = array(
      0 => array (
        'id'   => 27,
        'name' => 'Clean Guitar'
      ),
      1 => array (
        'id'   => 54,
        'name' => 'Syn Choir'
      )
    );

    $this->assertArraySubset($expected, $this->tablature->getInstruments());
    $this->assertArraySubset($expected[0], $this->tablature->getInstrument(0));

    # MeasureHeaders
    $this->assertEquals(4, $this->tablature->countMeasureHeaders());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Music\\MeasureHeader', $this->tablature->getMeasureHeaders());
    $this->assertInstanceOf('PhpTabs\\Music\\MeasureHeader', $this->tablature->getMeasureHeader(0));

    $this->assertInstanceOf('PhpTabs\\Component\\Tablature', $this->tablature->getTablature());
  }

  public function tearDown()
  {
    unset($this->tablature);
  }
}
