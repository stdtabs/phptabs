<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component\Importer;

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\Component\Importer\TempoParser;

/**
 * Tests ParserBase exceptions
 */
class ParserBaseTest extends PHPUnit_Framework_TestCase
{
  /**
   * Test parse method exceptions when no argument
   * 
   * @expectedException Exception
   */
  public function testNoArgumentExceptions()
  {
    $parser = new TempoParser(1);

    $parser->parseTempo();
  }

  /**
   * Test parse method exceptions when too many arguments
   * 
   * @expectedException Exception
   */
  public function testTooManyArgumentsException()
  {
    $parser = new TempoParser(1);

    $parser->parseTempo(1, 2, 3);
  }

  /**
   * Test method exception when method is unknown
   * 
   * @expectedException Exception
   */
  public function testUnknownMethodException()
  {
    $parser = new TempoParser(1);

    $parser->unknownPrefixTempo(1, 2, 3);
  }
}
