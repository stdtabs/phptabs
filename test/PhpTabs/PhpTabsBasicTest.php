<?php

namespace PhpTabs\Test;

use PhpTabs\PhpTabs;

class PhpTabsBasicTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->tablature = new PhpTabs();
  }

  public function tearDown()
  {
    unset($this->tablature);
  }

  /**
   * Tests write mode with empty attributes
   */
  public function testWriteModeWithEmptyAttributes()
  {
    # Errors
    $this->assertEquals(false, $this->tablature->hasError());
    $this->assertEquals(null, $this->tablature->getError());

    # Meta attributes
    $this->assertEquals('', $this->tablature->getName());
    $this->assertEquals('', $this->tablature->getArtist());
    $this->assertEquals('', $this->tablature->getAlbum());
    $this->assertEquals('', $this->tablature->getAuthor());
    $this->assertEquals('', $this->tablature->getCopyright());
    $this->assertEquals('', $this->tablature->getWriter());
    $this->assertEquals('', $this->tablature->getComments());
    $this->assertEquals('', $this->tablature->getDate());
    $this->assertEquals('', $this->tablature->getTranscriber());

    # Tracks
    $this->assertEquals(0, $this->tablature->countTracks());
    $this->assertEquals(array(), $this->tablature->getTracks());
    $this->assertEquals(null, $this->tablature->getTrack(42));

    # Channels
    $this->assertEquals(0, $this->tablature->countChannels());
    $this->assertEquals(array(), $this->tablature->getChannels());
    $this->assertEquals(null, $this->tablature->getChannel(42));

    # MeasureHeaders
    $this->assertEquals(0, $this->tablature->countMeasureHeaders());
    $this->assertEquals(array(), $this->tablature->getMeasureHeaders());
    $this->assertEquals(null, $this->tablature->getMeasureHeader(42));

    # Instruments
    $this->assertEquals(0, $this->tablature->countInstruments());
    $this->assertEquals(array(), $this->tablature->getInstruments());
    $this->assertEquals(null, $this->tablature->getInstrument(42));
    
    $this->assertInstanceOf('PhpTabs\\Component\\Tablature', $this->tablature->getTablature());
  }

  /**
   * Tests read mode with a non readable file
   */
  public function testReadModeWithNonReadableFile()
  {
    # Path not reachable
    $this->tablature = new PhpTabs('thisFileDoesNotExist.gp3');

    # Errors
    $this->assertEquals(true, $this->tablature->hasError());
    $this->assertEquals('Path thisFileDoesNotExist.gp3 is not readable'
      , $this->tablature->getError());

    # Given path is a directory
    $this->tablature = new PhpTabs(__DIR__);

    # Errors
    $this->assertEquals(true, $this->tablature->hasError());
    $this->assertEquals('Path must be a file. "' . __DIR__ . '" given'
      , $this->tablature->getError());
  }
}
