<?php

namespace PhpTabsTest\Component;

use PHPUnit_Framework_TestCase;
use PhpTabs\Component\Reader;
use PhpTabs\Component\File;

class ReaderTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException Exception
   */
  public function testNotAllowedExtension()
  {
    $filename = PHPTABS_TEST_BASEDIR . '/samples/testNotAllowedExtension.xxx';
    new Reader( new File($filename) );
  }
}
