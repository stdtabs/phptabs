<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component\Exporter;

use DOMNode;
use DOMXPath;
use PHPUnit\Framework\TestCase;

abstract class XmlTestCaseHelper extends TestCase
{
    abstract protected function getXmlDocument();

    protected function assertXpathMatch($expected, $xpath, $message = null, DOMNode $context = null)
    {
        $dom = $this->getXmlDocument();

        $xpathDom = new DOMXPath($dom);

        $context = $context === null
            ? $dom->documentElement
            : $context;

        $result = $xpathDom->evaluate($xpath, $context);

        $this->assertEquals($expected, $result, $message);
    }
}
