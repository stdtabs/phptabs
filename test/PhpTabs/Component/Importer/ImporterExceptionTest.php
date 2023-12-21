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
use PHPUnit\Framework\TestCase;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;

/**
 * Tests Importer exceptions
 */
class ImporterExceptionTest extends TestCase
{
    /**
     * Provider
     */
    public static function getAllParsers()
    {
        $names = [
        'Beat',
        'ChannelParameter',
        'Channel',
        'Chord',
        'Color',
        'Duration',
        'EffectPoints',
        'Grace',
        'Harmonic',
        'Lyrics',
        'Marker',
        'MeasureHeader',
        'Measure',
        'NoteEffect',
        'Note',
        'Song',
        'String',
        'Text',
        'TimeSignature',
        'Track',
        'TremoloPicking',
        'Trill',
        'Voice'
        ];

        $parsers = [];

        foreach ($names as $name) {
            $param1 = [];
            $param2 = null;

            if ($name == 'Song') {
                $param2 = new Song();
            } elseif ($name == 'Track') {
                $param2 = new Song();
            } elseif ($name == 'Voice') {
                $param1 = 0;
                $param2 = [];
            } elseif ($name == 'Measure') {
                $param2 = new MeasureHeader();
            }

            $parsers[] = ['PhpTabs\\Component\\Importer\\' . $name . 'Parser', $param1, $param2];
        }

        return $parsers;
    }

    /**
     * Test importer exceptions
     *
     * @dataProvider      getAllParsers()
     */
    public function testExceptions($name, $data, $param2)
    {
        $this->expectException(Exception::class);

        new $name($data, $param2);
    }
}
