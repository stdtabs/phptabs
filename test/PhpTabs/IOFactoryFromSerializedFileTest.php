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
use PHPUnit_Framework_TestCase;
use PhpTabs\IOFactory;

/**
 * Tests IOFactory::fromSerializedFile()
 */
class IOFactoryFromSerializedFileTest extends PHPUnit_Framework_TestCase
{
  /**
   * A provider for various scenarios that throw \Exception 
   */
  public function getExceptionScenarios()
  {
    return [
      [['ee']], # Array as filename
      [1.25],   # Float as filename
      [PHPTABS_TEST_BASEDIR . '/sample'],   # Unreadable filename  
      [PHPTABS_TEST_BASEDIR . '/samples/'], # Dir as filename 
      [PHPTABS_TEST_BASEDIR . '/samples/testSimpleMidi.mid']  # Not a valid serialized file   
    ];
  }

  /**
   * @dataProvider      getExceptionScenarios
   * @expectedException Exception
   */
  public function testExceptionScenario($filename)
  {
    IOFactory::fromSerializedFile($filename);
  }

  /**
   * Provide all serialized & source files
   */
  public function getAllSampleTabs()
  {
    $files = glob(
      PHPTABS_TEST_BASEDIR 
      . '/samples/testS*'
    );

    $filenames = [];

    foreach ($files as $filename) {
      $serFilename = str_replace(
        '/samples/',
        '/files/serialized/',
        $filename
      ) . '.ser';
      $filenames[] = [$filename, $serFilename];
    }

    return $filenames;
  }

  /**
   * Test simple tabs bijection
   * 
   * @dataProvider getAllSampleTabs()
   */
  public function testSimpleTabsBijection($filename, $serFilename)
  {
    $tabs     = IOFactory::fromFile($filename);
    $expected = $tabs->export();
    $import   = IOFactory::fromSerializedFile($serFilename);

    $this->assertEquals(
      $expected,
      $import->export(),
      "Simple tabs '$filename' fromSerializedFile() fails"
    );
  }
}
