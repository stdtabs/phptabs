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
use PhpTabs\Component\Config;
use PhpTabs\Component\Log;

/**
 * Tests Log component
 */
class LogTest extends PHPUnit_Framework_TestCase
{
  public function testLog()
  {
    Log::clear();
    
    Config::set('verbose', true);

    # Empty log
    $this->assertEquals(0, Log::countLogs());
    $this->assertEquals(array(), Log::tail(4));

    # Adds a default type message
    $this->expectOutputString("\n[NOTICE] Log with default type");
    Log::add('Log with default type');


    $expected = array(
      0 => array(
        'type'    =>'NOTICE',
        'message' => 'Log with default type'
      )
    );
    $this->assertEquals($expected, Log::tail(42));
    $this->assertEquals($expected, Log::tail(1));

    # counts an unexisting key
    $this->assertEquals(0, Log::countLogs(42));

    # Counts an existing key
    $this->assertEquals(1, Log::countLogs('NOTICE'));

    Config::set('verbose', false);
  }
}
