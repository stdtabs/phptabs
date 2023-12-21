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
use PhpTabs\Music\Duration;
use PhpTabs\Music\Marker;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\PhpTabs;

use PHPUnit\Framework\TestCase;

/**
 * Create a new song from a sliced one
 */
class PhpTabsSliceMeasuresTest extends TestCase
{
    /**
     * Create a 5 tracks / 5 measures song that will be dispatched
     * in all tests
     */
    public static function getScenarios()
    {
        $song = new PhpTabs();

        // Define 6 strings
        foreach ([64, 59, 55, 50, 45, 40] as $index => $value) {
            $string = new TabString($index + 1, $value);
        }

        // Create 5 tracks
        $tracks = [];
        for ($i = 0; $i < 5; $i++) {
            $tracks[$i] = new Track();
            $tracks[$i]->setName('Track ' . $i);
            // One channel
            $channel = new Channel();
            $channel->setId($i + 1);

            $tracks[$i]->addString($string);

            // Attach channel to the song
            $song->addChannel($channel);
            $tracks[$i]->setChannelId($channel->getId());
            $song->addTrack($tracks[$i]);

            // 5 measures
            for ($j = 0; $j < 5; $j++) {
                // Create custom tempo for MeasureHeader
                $tempo = new Tempo();
                $tempo->setValue(180 + $j);

                $timeSignature = new TimeSignature();
                $timeSignature->setNumerator(4);

                // One measure header, will be shared by all first measures of all tracks
                if ($i == 0) {
                    $mh = new MeasureHeader();
                    $mh->setNumber($j + 1);
                    $mh->setTempo($tempo);
                    $mh->setTimeSignature($timeSignature);
                    // Attach measure header to the song
                    $song->addMeasureHeader($mh);
                }

                // One specific measure for the first track,
                // with a MeasureHeader as only parameter
                $measure = new Measure($song->getMeasureHeader($j));

                // Add measure to the track
                $tracks[$i]->addMeasure($measure);
            }
        }

        return [
            'only-1st' => [clone $song, 0, 0],
            'only-3rd-and-fourth' => [clone $song, 2, 3],
            'only-5th' => [clone $song, 4, 4],
        ];
    }

    /**
     * We check that there is only a certain count of tracks
     * and that they're the right ones
     *
     * @dataProvider getScenarios
     */
    public function testSliceMeasures($song, $fromMeasureIndex, $toMeasureIndex)
    {
        $sliced = $song->sliceMeasures($fromMeasureIndex, $toMeasureIndex);

        // Measure count
        $this->assertEquals(
            $toMeasureIndex - $fromMeasureIndex + 1,
            $sliced->countMeasureHeaders()
        );

        // Measures tempo
        foreach ($sliced->getTracks() as $track) {
            $this->assertEquals(
                $toMeasureIndex - $fromMeasureIndex + 1,
                $track->countMeasures()
            );

            for ($i = 0; $i < $track->countMeasures(); $i++) {
                $this->assertEquals(
                    180 + ($fromMeasureIndex + $i),
                    $track->getMeasure($i)->getTempo()->getValue()
                );
            }
        }
    }
}
