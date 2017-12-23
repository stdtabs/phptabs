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

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests export track by track
 */
class TrackExporterTest extends PHPUnit_Framework_TestCase
{
  public function getSimpleFiles()
  {
    $files = glob(PHPTABS_TEST_BASEDIR . '/samples/testS*');

    $filenames = [];

    foreach ($files as $filename) {
      $filenames[] = [
        $filename
      ];
    }

    return $filenames;
  }

  /**
   * Track-by-Track exports
   * 
   * @dataProvider getSimpleFiles
   */
  public function testExporter($source)
  {
    $tabs = new PhpTabs($source);
    $countTracks = $tabs->countTracks();

    $checkPoints = [
      'name'      => 'getName',
      'nbMeasure' => 'countMeasures',
      'channelId' => 'getChannelId',
      'number'    => 'getNumber',
      'offset'    => 'getOffset',
      'lyrics'    => 'getLyrics',
      'color'     => 'getColor',
      'strings'   => 'getStrings'
    ];

    foreach ($tabs->getTracks() as $index => $track) {
      // Check point values
      foreach ($checkPoints as $varname => $func) {
        $$varname  = $track->$func();
      }

      # Make exports, import
      $exported  = (new PhpTabs())->import(
        $tabs->exportTrack($index)
      );

      # Non invasive export
      $this->assertEquals(
        $countTracks,
        $tabs->countTracks(),
        'Track number have changed during export'
      );

      # Test properties
      foreach ($checkPoints as $varname => $func) {
        $this->assertEquals(
          $$varname,
          $exported->getTrack(0)->$func(),
          "Track '$varname' have changed during export"
        );
      }

    }
  }

  /**
   * @dataProvider getSimpleFiles
   * @expectedException Exception
   */
  public function testUnexistingTrack($filename)
  {
    $tabs = new PhpTabs($filename);

    $tabs->exportTrack(rand(2, 85));
  }
}
