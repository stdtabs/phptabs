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

use PHPUnit\Framework\TestCase;
use PhpTabs\Component\Config;
use PhpTabs\Component\Log;

/**
 * Tests Log component
 */
class LogTest extends TestCase
{
    public function testLog()
    {
        Log::clear();
    
        Config::set('verbose', true);

      # Empty log
        $this->assertEquals(0, Log::countLogs());
        $this->assertEquals([], Log::tail(4));

      # Adds a default type message
        $lineBreaks = "/\r?\n/"; // take care of newline-encodings
        $expected = preg_replace($lineBreaks, PHP_EOL, "\n[NOTICE] Log with default type");
        $this->expectOutputString($expected);
        Log::add('Log with default type');

        $expected = [
            0 => [
                'type'    =>'NOTICE',
                'message' => 'Log with default type'
            ]
        ];
        $this->assertEquals($expected, Log::tail(42));
        $this->assertEquals($expected, Log::tail(1));

      # counts an unexisting key
        $this->assertEquals(0, Log::countLogs(42));

      # Counts an existing key
        $this->assertEquals(1, Log::countLogs('NOTICE'));

        Config::set('verbose', false);
    }
}
