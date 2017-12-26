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
 * Tests IOFactory::fromFile()
 */
class IOFactoryFromFileTest extends PHPUnit_Framework_TestCase
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
      [PHPTABS_TEST_BASEDIR . '/samples/'],  # Dir as filename 
    ];
  }

  /**
   * @dataProvider      getExceptionScenarios
   * @expectedException Exception
   */
  public function testExceptionScenario($filename)
  {
    IOFactory::fromFile($filename);
  }

  /**
   * Provide all JSON & source files
   */
  public function getAllSampleTabs()
  {
    $files = glob(
      PHPTABS_TEST_BASEDIR 
      . '/samples/testS*'
    );

    $filenames = [];

    foreach ($files as $filename) {
      $jsonFilename = str_replace(
        '/samples/',
        '/files/json/',
        $filename
      ) . '.json';
      $serializedFilename = str_replace(
        '/samples/',
        '/files/serialized/',
        $filename
      ) . '.ser';
      $filenames[] = [$filename, $jsonFilename, 'json'];
      $filenames[] = [$filename, $serializedFilename, 'ser'];
    }

    return $filenames;
  }

  /**
   * Test simple tabs bijection WITHOUT type parameter
   * 
   * @dataProvider getAllSampleTabs()
   */
  public function testSimpleTabsBijection($filename, $destFilename, $type)
  {
    $expected = IOFactory::fromFile($filename)->export();
    $import   = IOFactory::fromFile($destFilename);

    $this->assertEquals(
      $expected,
      $import->export(),
      "Simple tabs '$filename' fromFile() fails"
    );
  }


  /**
   * Test simple tabs bijection WITH type parameter
   * 
   * @dataProvider getAllSampleTabs()
   */
  public function testSimpleTabsBijectionWithType($filename, $destFilename, $type)
  {
    $expected = IOFactory::fromFile($filename)->export();
    $import   = IOFactory::fromFile($destFilename, $type);

    $this->assertEquals(
      $expected,
      $import->export(),
      "Simple tabs '$filename' fromFile() fails"
    );
  }

  /**
   * @dataProvider      getAllSampleTabs
   * @expectedException Exception
   */
  public function testExceptionScenarioWithGivenTypeError($filename, $destFilename, $type)
  {
    $wrongType = $type == 'json' ? 'ser' : 'json';

    IOFactory::fromFile($destFilename, $wrongType);
  }
}
