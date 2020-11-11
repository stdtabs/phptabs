<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Error;
use PhpTabs\IOFactory;

/**
 * Tests PhpTabs component
 */
class PhpTabsTest extends TestCase
{
  /**
   * Test getVersion()
   */
    public function testGetVersion()
    {
        // PHPUnit >= 9
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '/\d.\d.\d/',
                IOFactory::create()->getVersion(),
                'getVersion failed'
            );
        // PHPUnit < 9
        } else {
            $this->assertRegExp(
                '/\d.\d.\d/',
                IOFactory::create()->getVersion(),
                'getVersion failed'
            );
        }
    }
}
