<?php

namespace PhpTabs\Test;

use PhpTabs\PhpTabs;
use PhpTabs\Component\Tablature;
use PhpTabs\Component\File;


class PhpTabsTest extends \PHPUnit_Framework_TestCase
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


  /**
   * Tests read mode with a simple tablature
   * Guitar Pro 3
   */
  public function testReadModeWithSimpleGuitarPro3Tab()
  {
    $tablature = new PhpTabs(__DIR__ . '/samples/testSimpleTab.gp3');

    # Errors
    $this->assertEquals(false, $tablature->hasError());
    $this->assertEquals(null, $tablature->getError());
    
    # Meta attributes
    $this->assertEquals('Testing name', $tablature->getName());
    $this->assertEquals('Testing artist', $tablature->getArtist());
    $this->assertEquals('Testing album', $tablature->getAlbum());
    $this->assertEquals('Testing author', $tablature->getAuthor());
    $this->assertEquals('Testing copyright', $tablature->getCopyright());
    $this->assertEquals('Testing writer', $tablature->getWriter());
    $this->assertEquals("Testing comments line 1\nTesting comments line 2"
      , $tablature->getComments());
    $this->assertEquals('', $tablature->getDate());       #Not supported by Guitar Pro 3
    $this->assertEquals('', $tablature->getTranscriber());#Not supported by Guitar Pro 3

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
    $this->assertEquals(3, $tablature->countMeasureHeaders());
    $this->assertContainsOnlyInstancesOf('PhpTabs\\Model\\MeasureHeader', $tablature->getMeasureHeaders());
    $this->assertEquals(null, $tablature->getMeasureHeader(42));
    $this->assertInstanceOf('PhpTabs\\Model\\MeasureHeader', $tablature->getMeasureHeader(0));
    
    $this->assertInstanceOf('PhpTabs\\Component\\Tablature', $tablature->getTablature());
  }
}
