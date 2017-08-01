<?php

namespace PhpTabsTest\Component;

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests Dumper component
 */
class DumperTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->tablature = new PhpTabs(
      PHPTABS_TEST_BASEDIR 
      . '/samples/testSimpleTab.gp5'
    );
  }

  /**
   * Following dumps must be a string
   * - text
   * - txt
   * - yaml
   * - yml
   * - serialize
   * - json
   * - var_export
   * - xml
   */
  public function testStringFormats()
  {
    foreach ([
      'text',
      'txt',
      'yaml',
      'yml',
      'serialize',
      'json',
      'var_export',
      'xml'
    ] as $format
    ) {
      $this->assertTrue(
        is_string(
          $this->tablature->dump($format)
        )
      );
    }
  }

  /**
   * Following dumps must be an array
   * - array
   * - none
   */
  public function testArrayFormats()
  {
    foreach ([
      'array',
      null
    ] as $format
    ) {
      $this->assertTrue(
        is_array(
          $this->tablature->dump($format)
        )
      );
    }
  }

  /**
   * Some dump format parameters have aliases
   * - text = txt
   * - yaml = yml
   */
  public function testAliases()
  {
    $this->assertSame(
      $this->tablature->dump('text'),
      $this->tablature->dump('txt')
    );
    
    $this->assertSame(
      $this->tablature->dump('yaml'),
      $this->tablature->dump('yml')
    );
  }

  /**
   * @expectedException Exception
   */
  public function testException()
  {
    # Not a valid dump format
    (new PhpTabs())->dump('exception');
  }
}
