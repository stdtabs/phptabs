<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Music;

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\IOFactory;

/**
 * Tests voice methods
 */
class VoiceTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->tablature = IOFactory::fromFile(
      PHPTABS_TEST_BASEDIR 
      . '/samples/testSimpleTab.gp5'
    );
  }

  /**
   * Test getTime() shortcut
   */
  public function testVoiceDurationInSeconds()
  {
    // First beat / first measure / first track
    // Tempo 66 / timeSignature 12/8
    $duration = $this->tablature
        ->getTrack(0)
        ->getMeasure(0)
        ->getBeat(0)
        ->getVoice(0)
        ->getTime();

    $this->assertEquals(7.2727, $duration, '', 0.0001);

    // First beat / second measure / first track
    // Tempo 88 / timeSignature 12/8 / dotted=true
    $duration = $this->tablature
        ->getTrack(0)
        ->getMeasure(1)
        ->getBeat(0)
        ->getVoice(0)
        ->getTime();

    $this->assertEquals(2.0454, $duration, '', 0.0001);
  }

  public function tearDown()
  {
    unset($this->tablature);
  }
}
