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
use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests Importer component
 */
class ImporterTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->tablature = new PhpTabs(
      PHPTABS_TEST_BASEDIR 
      . '/samples/testSimpleTab.gp5'
    );
  }

  /**
   * Test empty tabs bijection
   */
  public function testEmptyTabsBijection()
  {
    $tabs     = new PhpTabs();
    $expected = $tabs->export();
    $result   = $tabs->import($tabs->export())->export();

    $this->assertEquals(
      $expected,
      $result,
      'Empty tabs export-import fails'
    );
  }

  /**
   * Provider
   */
  public function getAllSampleTabs()
  {
    $files = glob(
      PHPTABS_TEST_BASEDIR 
      . '/samples/testS*'
    );

    $filenames = [];

    foreach ($files as $filename) {
      $filenames[] = [$filename];
    }

    return $filenames;
  }

  /**
   * Test simple tabs bijection
   * 
   * @dataProvider getAllSampleTabs()
   */
  public function testSimpleTabsBijection($filename)
  {
    $tabs     = new PhpTabs($filename);
    $expected = $tabs->export();
    $import   = (new PhpTabs())->import($expected);

    $this->assertEquals(
      $expected,
      $import->export(),
      "Simple tabs '$filename' export-import fails"
    );
  }

  /**
   * @expectedException Exception
   */
  public function testImportException()
  {
    # Not a valid import format
    (new PhpTabs())->import([]);
  }
}
