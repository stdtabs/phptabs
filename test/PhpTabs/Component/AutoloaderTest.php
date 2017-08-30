<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component;

use Exception;

use PHPUnit_Framework_TestCase;
use PhpTabs\Component\Autoloader;
use PhpTabs\Music\MeasureHeader;

/**
 * Tests Autoloader component
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
  # Existing class
  public function testExistingClass()
  {
    Autoloader::register();
    
    $this->assertInstanceOf('PhpTabs\\Music\\MeasureHeader', new MeasureHeader());
  }
}
