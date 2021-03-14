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

namespace PhpTabs\Reader\GuitarPro;

use Exception;
use PhpTabs\Component\Config;
use PhpTabs\Component\InputStream;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\Lyric;
use PhpTabs\Music\Song;
use PhpTabs\Music\TimeSignature;

class GuitarPro5Reader extends GuitarProReaderBase
{
    /**
     * @var array $supportedVersions
     */
    private static $supportedVersions = [
        'FICHIER GUITAR PRO v5.00',
        'FICHIER GUITAR PRO v5.10'
    ];

    /**
     * @var integer $keySignature
     */
    protected $keySignature;

    /**
     * @var \PhpTabs\Component\Tablature
     */
    protected $tablature;

    /**
     * @param \PhpTabs\Component\InputStream $file An input file to read
     */
    public function __construct(InputStream $file)
    {
        parent::__construct($file);

        $this->readVersion();

        if (!$this->isSupportedVersion($this->getVersion())) {
            $this->closeStream();

            throw new Exception(sprintf('Version "%s" is not supported', $this->getVersion()));
        }

        $song = new Song();

        $this->setTablature($song);

        $this->factory('GuitarPro5Informations')->readInformations($song);

        // Meta only
        if (Config::get('type') == 'meta') {
            $this->closeStream();
            return;
        }

        $lyricTrack = $this->readInt();
        $lyric = $this->factory('GuitarProLyric')->readLyrics();

        $this->readSetup();

        $tempoValue = $this->readInt();

        if ($this->getVersionIndex() > 0) {
            $this->skip(1);
        }

        $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
        $this->skip(3);

        $this->readByte();

        $channels = $this->factory('GuitarProChannels')->readChannels();

        $this->skip(42);

        $measures = $this->readInt();
        $tracks = $this->readInt();

        $this->readMeasureHeaders($song, $measures);
        $this->readTracks($song, $tracks, $channels, $lyric, $lyricTrack);

        $this->skip($this->getVersionIndex() == 0 ? 2 : 1);

        // Meta+channels+tracks+measure headers only
        if (Config::get('type') == 'channels') {
            $this->closeStream();
            return;
        }

        $this->factory('GuitarPro5Measures')->readMeasures($song, $measures, $tracks, $tempoValue);

        $this->closeStream();
    }

    /**
     * @Get an array of supported versions
     */
    public function getSupportedVersions(): array
    {
        return self::$supportedVersions;
    }

    /**
     * {@inheritdoc}
     */
    public function getTablature(): Tablature
    {
        return isset($this->tablature)
            ? $this->tablature
            : new Tablature();
    }

    /**
     * Initialize Tablature with a Song
     */
    private function setTablature(Song $song): void
    {
        if (is_null($this->tablature)) {
            $this->tablature = new Tablature();
        }

        $this->tablature->setSong($song);
        $this->tablature->setFormat('gp5');
    }

    /*-------------------------------------------------------------------
    * Private methods are below
    * -----------------------------------------------------------------*/

    /**
     * Loop on measure headers to read
     */
    private function readMeasureHeaders(Song $song, int $count): void
    {
        $timeSignature = new TimeSignature();

        for ($i = 0; $i < $count; $i++) {
            if ($i > 0) {
                $this->skip();
            }

            $song->addMeasureHeader(
                $this->factory('GuitarPro5MeasureHeader')->readMeasureHeader($i, $timeSignature)
            );
        }
    }

    /**
     * Reads setup informations
     */
    private function readSetup(): void
    {
        $this->skip($this->getVersionIndex() > 0 ? 49 : 30);
        for ($i = 0; $i < 11; $i++) {
            $this->skip(4);
            $this->readStringByte(0);
        }
    }

    /**
     * Loop on tracks to read
     */
    private function readTracks(Song $song, int $count, array $channels, Lyric $lyric, int $lyricTrack): void
    {
        for ($number = 0; $number < $count; $number++) {
            $track = $this->factory('GuitarPro5Track')->readTrack(
                $song, $channels,
                $number + 1 == $lyricTrack ? $lyric : new Lyric()
            );

            $song->addTrack($track);
        }
    }
}
