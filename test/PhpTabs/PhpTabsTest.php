<?php

namespace PhpTabsTest\Component;

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests PhpTabs component
 */
class PhpTabsTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testException()
  {
    # Not a valid number of params
    (new PhpTabs())->dump('param1', 'param2', 'param3');
  }
}
