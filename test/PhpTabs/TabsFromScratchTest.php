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

use PhpTabs\Music\Channel;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\PhpTabs;

use PHPUnit\Framework\TestCase;

/**
 * Create tabs from scratch
 */
class TabsFromScratchTest extends TestCase
{
    public static function getScenarios()
    {
        $song = new PhpTabs();

        // Create one track
        $track = new Track();
        $track->setName('Track 1');

        // Define 6 strings
        foreach ([64, 59, 55, 50, 45, 40] as $index => $value) {
            $string = new TabString($index + 1, $value);
            $track->addString($string);
        }

        // One channel
        $channel = new Channel();
        $channel->setId(1);

        // Attach channel to the song
        $song->addChannel($channel);
        $track->setChannelId($channel->getId());
        $song->addTrack($track);

        $tempo_array      = [200, 100, 150];
        $numerator_array  = [3, 4, 7];

        $number_of_measures = 3;

        for( $i = 0; $i < $number_of_measures; $i++) {
            // Create custom tempo for MeasureHeader
            $tempo = new Tempo();
            $tempo->setValue($tempo_array[$i]);

            $timeSignature = new TimeSignature();
            $timeSignature->setNumerator($numerator_array[$i]);

            // One measure header, will be shared by all first measures of all tracks
            $mh = new MeasureHeader();
            $mh->setNumber($i+1);
            $mh->setTempo($tempo);
            $mh->setTimeSignature($timeSignature);

            // One specific measure for the first track,
            // with a MeasureHeader as only parameter
            $measure = new Measure($mh);

            // Attach measure header to the song
            $song->addMeasureHeader($mh);

            // Add measure to the track
            $track->addMeasure($measure);
        }

        return [
            'guitar-pro-3' => [clone $song, 'gp3'],
            'guitar-pro-4' => [clone $song, 'gp4'],
            'guitar-pro-5' => [clone $song, 'gp5'],
        ];
    }

    /**
     * Test that a tab from scratch can be converted to GuitarPro formats
     *
     * @dataProvider getScenarios
     */
    public function testFromScratchToGuitarPro($song, $format)
    {
        $expected = file_get_contents(PHPTABS_TEST_BASEDIR . '/samples/from-scratch.' . $format);
        $content = $song->convert($format);

        $this->assertEquals(
            $expected,
            $content
        );
    }
}
