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
