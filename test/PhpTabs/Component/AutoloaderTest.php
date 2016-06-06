<?php

namespace PhpTabsTest\Component;

use Exception;

use PHPUnit_Framework_TestCase;
use PhpTabs\Component\Autoloader;
use PhpTabs\Model\MeasureHeader;

/**
 * Tests Autoloader component
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
  # Existing class
  public function testExistingClass()
  {
    Autoloader::register();
    
    $this->assertInstanceOf('PhpTabs\\Model\\MeasureHeader', new MeasureHeader());
  }
}
