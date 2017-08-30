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

/**
 * Tests Config component
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
  public function testConfig()
  {
    Config::clear();

    # bad key format scenario
    $this->assertEquals(null, Config::get(array(42)));
    
    Config::set(array(42), 42);

    $this->assertEquals(null, Config::get(array(42)));
    
    # sets a good key
    Config::set('Sense', 42);
    $this->assertEquals(42, Config::get('Sense'));
    
    # Gets all configs
    $expected = array(
      'Sense' => 42
    );
    $this->assertEquals($expected, Config::getAll());
  }
}
