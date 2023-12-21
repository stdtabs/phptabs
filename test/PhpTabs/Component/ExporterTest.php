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
use PhpTabs\PhpTabs;

/**
 * Tests Exporter component
 */
class ExporterTest extends TestCase
{
    protected function setUp() : void
    {
        $this->tablature = new PhpTabs(
            PHPTABS_TEST_BASEDIR
            . '/samples/testSimpleTab.gp5'
        );
    }

    /**
     * Some export format parameters have aliases
     * - text = txt
     * - yaml = yml
     */
    public function testAliases()
    {
        $this->assertSame(
            $this->tablature->convert('text'),
            $this->tablature->toText()
        );

        $this->assertSame(
            $this->tablature->convert('yaml'),
            $this->tablature->toYaml()
        );
    }
}
