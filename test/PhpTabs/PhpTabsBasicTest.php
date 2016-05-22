<?php

namespace PhpTabsTest;

use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

class BasicsTest extends PHPUnit_Framework_TestCase
{
  /**
   * Tests write mode with empty attributes
   */
  public function testWriteModeWithEmptyAttributes()
  {
    $tablature = new PhpTabs();

    # Errors
    $this->assertEquals(false, $tablature->hasError());
    $this->assertEquals(null, $tablature->getError());

    # Meta attributes
    $this->assertEquals('', $tablature->getName());
    $this->assertEquals('', $tablature->getArtist());
    $this->assertEquals('', $tablature->getAlbum());
    $this->assertEquals('', $tablature->getAuthor());
    $this->assertEquals('', $tablature->getCopyright());
    $this->assertEquals('', $tablature->getWriter());
    $this->assertEquals('', $tablature->getComments());
    $this->assertEquals('', $tablature->getDate());
    $this->assertEquals('', $tablature->getTranscriber());

    # Tracks
    $this->assertEquals(0, $tablature->countTracks());
    $this->assertEquals(array(), $tablature->getTracks());
    $this->assertEquals(null, $tablature->getTrack(42));

    # Channels
    $this->assertEquals(0, $tablature->countChannels());
    $this->assertEquals(array(), $tablature->getChannels());
    $this->assertEquals(null, $tablature->getChannel(42));

    # MeasureHeaders
    $this->assertEquals(0, $tablature->countMeasureHeaders());
    $this->assertEquals(array(), $tablature->getMeasureHeaders());
    $this->assertEquals(null, $tablature->getMeasureHeader(42));

    # Instruments
    $this->assertEquals(0, $tablature->countInstruments());
    $this->assertEquals(array(), $tablature->getInstruments());
    $this->assertEquals(null, $tablature->getInstrument(42));
    
    $this->assertInstanceOf('PhpTabs\\Component\\Tablature', $tablature->getTablature());
  }

  /**
   * Tests read mode with a non readable file
   */
  public function testReadModeWithNonReadableFile()
  {
    # Path not reachable
    $tablature = new PhpTabs('thisFileDoesNotExist.gp3');

    # Errors
    $this->assertEquals(true, $tablature->hasError());
    $this->assertEquals('Path thisFileDoesNotExist.gp3 is not readable'
      , $tablature->getError());

    # Given path is a directory
    $tablature = new PhpTabs(__DIR__);

    # Errors
    $this->assertEquals(true, $tablature->hasError());
    $this->assertEquals('Path must be a file. "' . __DIR__ . '" given'
      , $tablature->getError());
  }
}
