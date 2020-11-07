<?php

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
use PhpTabs\Component\FileInput;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;
use PhpTabs\Music\TimeSignature;

class GuitarPro3Reader extends GuitarProReaderBase
{
    /**
     * @var \PhpTabs\Component\Tablature
     */
    protected $tablature;

    /**
     * @var array $supportedVersions
     */
    private static $supportedVersions = ['FICHIER GUITAR PRO v3.00'];

    /**
     * @var boolean $tripletFeel
     * @var integer $keySignature
     */
    protected $tripletFeel, $keySignature;

    /**
     * @param \PhpTabs\Component\FileInput $file An input file to read
     */
    public function __construct(FileInput $file)
    {
        parent::__construct($file);

        $this->readVersion();

        if (!$this->isSupportedVersion($this->getVersion())) {
            $this->closeStream();

            throw new Exception(sprintf('Version "%s" is not supported', $this->getVersion()));
        }

        $song = new Song();

        $this->setTablature($song);

        $this->factory('GuitarPro3Informations')->readInformations($song);

        $this->tripletFeel = $this->readBoolean()
            ? MeasureHeader::TRIPLET_FEEL_EIGHTH
            : MeasureHeader::TRIPLET_FEEL_NONE;

        $tempoValue = $this->readInt();

        $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
        $this->skip(3);

        // Meta only
        if (Config::get('type') == 'meta') {
            return $this->closeStream();
        }

        $channels = $this->factory('GuitarProChannels')->readChannels();

        $measures = $this->readInt();
        $tracks = $this->readInt();

        $this->readMeasureHeaders($song, $measures);
        $this->readTracks($song, $tracks, $channels);

        // Meta+channels+tracks+measure headers only
        if (Config::get('type') == 'channels') {
            return $this->closeStream();
        }

        $this->factory('GuitarPro3Measures')->readMeasures($song, $measures, $tracks, $tempoValue);

        $this->closeStream();
    }

    /**
     * Get an array of supported versions
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
     * Initializes Tablature with read Song
     */
    private function setTablature(Song $song): void
    {
        if (!isset($this->tablature)) {
            $this->tablature = new Tablature();
        }

        $this->tablature->setSong($song);
        $this->tablature->setFormat('gp3');
    }

    /*-------------------------------------------------------------------
    * Private methods are below
    * -----------------------------------------------------------------*/

    /**
     * Loops on mesure headers to read
     */
    private function readMeasureHeaders(Song $song, int $count): void
    {
        $timeSignature = new TimeSignature();

        for ($i = 0; $i < $count; $i++) {
            $song->addMeasureHeader(
                $this->factory('GuitarPro3MeasureHeader')
                     ->readMeasureHeader(($i + 1), $song, $timeSignature)
            );
        }
    }

    /**
     * Loops on tracks to read
     */
    private function readTracks(Song $song, $count, array $channels): void
    {
        for ($number = 0; $number < $count; $number++) {
            $song->addTrack(
                $this->factory('GuitarPro3Track')
                     ->readTrack($song, $channels)
            );
        }
    }
}
