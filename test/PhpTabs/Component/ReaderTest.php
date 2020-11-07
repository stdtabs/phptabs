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
use PhpTabs\Component\Reader;
use PhpTabs\Component\FileInput;

class ReaderTest extends TestCase
{
    public function testNotAllowedExtension()
    {
        $this->expectException(Exception::class);

        $filename = PHPTABS_TEST_BASEDIR . '/samples/testNotAllowedExtension.xxx';
        $file = new FileInput($filename);
        new Reader($file->getInputStream(), $file->getExtension());
    }
}
