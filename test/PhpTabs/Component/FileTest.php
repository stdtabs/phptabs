<?php

namespace PhpTabsTest\Component;

use PHPUnit_Framework_TestCase;
use PhpTabs\Component\File;

class FileTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->filename = PHPTABS_TEST_BASEDIR . '/samples/testNotAllowedExtension.xxx';
  }

  /**
   * Tests parts of path for File component
   */
  public function testPathParts()
  {
    $file = new File($this->filename);

    $this->assertEquals($this->filename, $file->getPath());
    $this->assertEquals(dirname($this->filename), $file->getDirname());
    $this->assertEquals('testNotAllowedExtension.xxx', $file->getBasename());
    
    
  }

  public function testStreamMethods()
  {
    # Reads stream position when stream has been closed
    $file = new File($this->filename);
    $file->closeStream();

    $this->assertEquals(false, $file->getStreamPosition());
    
    # Reads stream read with offset
    $file = new File($this->filename);
    $this->assertEquals('IER G', $file->getStream(5, 5));
  }

  /**
   * Pointer exception
   * @expectedException Exception
   */
  public function testPointerException()
  {
    $file = new File($this->filename);
    $file->getStream(1);
    $file->getStream(50000000);
  }
}
