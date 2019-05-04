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
use PHPUnit\Framework\TestCase;
use PhpTabs\Component\Tablature;
use PhpTabs\Component\Writer;

class WriterTest extends TestCase
{
    public function testNotAllowedFormatException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->save('xxx');
    }

    public function testEmptySongDefaultException()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->save();
    }

    /**
     * gp3
     */
    public function testEmptySongGp3Exception()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('gp3');
    }

    /**
     * gp4
     */
    public function testEmptySongGp4Exception()
    {
        $this->expectException(Exception::class);

        (new Writer(new Tablature()))->build('gp4');
    }
}
