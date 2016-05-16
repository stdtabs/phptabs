<?php

namespace PhpTabs\Test;

use PhpTabs\PhpTabs;

class PhpTabsMidiTest extends \PHPUnit_Framework_TestCase
{
  /**
   * Tests read mode with a simple tablature
   * Guitar Pro 3
   */
  public function testReadModeWithSimpleMidiFile()
  {
    $tablature = new PhpTabs(__DIR__ . '/samples/testSimpleMidi.mid');

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
    $this->assertEquals('', $tablature->getDate());       #Not supported by Midi
    $this->assertEquals('', $tablature->getTranscriber());#Not supported by Midi

    # Tracks
    $this->assertEquals(1, $tablature->countTracks());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Model\\Track', $tablature->getTracks());
    $this->assertEquals(null, $tablature->getTrack(42));
    $this->assertInstanceOf('PhpTabs\\Model\\Track', $tablature->getTrack(0));

    # Channels
    $this->assertEquals(1, $tablature->countChannels());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Model\\Channel', $tablature->getChannels());
    $this->assertEquals(null, $tablature->getChannel(42));
    $this->assertInstanceOf('PhpTabs\\Model\\Channel', $tablature->getChannel(0));

    # MeasureHeaders
    $this->assertEquals(1, $tablature->countMeasureHeaders());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Model\\MeasureHeader', $tablature->getMeasureHeaders());
    $this->assertEquals(null, $tablature->getMeasureHeader(42));
    $this->assertInstanceOf('PhpTabs\\Model\\MeasureHeader', $tablature->getMeasureHeader(0));

    # Instruments
    $this->assertEquals(1, $tablature->countInstruments());
    $expected = array(
      0 => array (
        'id'   => 0,
        'name' => 'Piano'
      )
    );
    $this->assertArraySubset($expected, $tablature->getInstruments());
    $this->assertEquals(null, $tablature->getInstrument(42));
    $this->assertArraySubset($expected[0], $tablature->getInstrument(0));
    
    $this->assertInstanceOf('PhpTabs\\Component\\Tablature', $tablature->getTablature());
  }
}
