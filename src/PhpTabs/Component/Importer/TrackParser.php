<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use PhpTabs\Music\Song;
use PhpTabs\Music\Track;

final class TrackParser extends ParserBase
{
    /**
     * @var array<string>
     */
    protected $required = [
        'number',
        'offset',
        'channelId',
        'solo',
        'mute',
        'name',
        'color',
        'lyrics',
        'measures',
        'strings',
    ];

    /**
     * Parse a track array
     * 
     * @param array $data
     */
    public function __construct(array $data, Song $song)
    {
        $this->checkKeys($data, $this->required);

        $track = new Track();
        $track->setNumber($data['number']);
        $track->setOffset($data['offset']);
        $track->setChannelId($data['channelId']);
        $track->setSolo($data['solo']);
        $track->setMute($data['mute']);
        $track->setName(strval($data['name']));

        $track->setColor(
            $this->parseColor($data['color'])
        );

        $track->setLyrics(
            $this->parseLyrics($data['lyrics'])
        );

        foreach ($data['measures'] as $index => $item) {
            $this->checkKeys($item, ['measure']);
            $track->addMeasure(
                $this->parseMeasure(
                    $item['measure'],
                    $song->getMeasureHeader($index)
                )
            );
        }

        foreach ($data['strings'] as $string) {
            $this->checkKeys($string, ['string']);
            $track->addString(
                $this->parseString($string['string'])
            );
        }

        $this->item = $track;
    }
}
