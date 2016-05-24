<?php

namespace PhpTabsTest\Component;

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests Dumper component
 */
class DumperTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException Exception
   */
  public function testException()
  {
    # Not a valid dump format
    (new PhpTabs())->dump('exception');
  }
}
