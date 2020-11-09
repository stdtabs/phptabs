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
use Exception;
use PhpTabs\Component\Tablature;

class TablatureTest extends TestCase
{
    public function testUnexistingMethod()
    {
        $this->expectException(Exception::class);

        // Method does not exist
        (new Tablature())->undefinedMethod();
    }

    public function testException()
    {
        $this->expectException(Exception::class);

        // Not a valid number of params
        (new Tablature())->getChannels('param1', 'param2', 'param3');
    }
}
