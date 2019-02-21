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
use PhpTabs\Component\Tablature;

class TablatureTest extends TestCase
{
    /**
     * @expectedException PHPUnit\Framework\Error\Error
     */
    public function testUnexistingMethod()
    {
        // Method does not exist
        (new Tablature())->undefinedMethod();
    }

    /**
     * @expectedException PHPUnit\Framework\Error\Error
     */
    public function testException()
    {
        // Not a valid number of params
        (new Tablature())->getChannels('param1', 'param2', 'param3');
    }
}
