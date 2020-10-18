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
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\Lyric;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\Song;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Velocities;

class GuitarPro4Reader extends GuitarProReaderBase
{
    /**
     * @var array $supportedVersions
     */
    private static $supportedVersions = ['FICHIER GUITAR PRO v4.00', 'FICHIER GUITAR PRO v4.06', 'FICHIER GUITAR PRO L4.06'];

    /**
     * @var int $tripletFeel
     * @var int $keySignature
     */
    protected $tripletFeel, $keySignature;

    /**
     * @var \PhpTabs\Component\Tablature
     */
    protected $tablature;

    /**
     * @param \PhpTabs\Component\File $file An input file to read
     */
    public function __construct(File $file)
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

        // Meta only
        if (Config::get('type') == 'meta') {
            $this->closeStream();
            return;
        }

        $lyricTrack = $this->readInt();
        $lyric = $this->factory('GuitarProLyric')->readLyrics();

        $tempoValue = $this->readInt();

        $this->keySignature = $this->factory('GuitarProKeySignature')->readKeySignature();
        $this->skip(3);

        $this->readByte();

        $channels = $this->factory('GuitarProChannels')->readChannels();

        $measures = $this->readInt();
        $tracks = $this->readInt();

        $this->readMeasureHeaders($song, $measures);
        $this->readTracks($song, $tracks, $channels, $lyric, $lyricTrack);

        // Meta+channels+tracks+measure headers only
        if (Config::get('type') == 'channels') {
            $this->closeStream();
            return;
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
        return !is_null($this->tablature)
            ? $this->tablature
            : new Tablature();
    }

    /**
     * Initialize Tablature with read Song
     */
    private function setTablature(Song $song): void
    {
        if (is_null($this->tablature)) {
            $this->tablature = new Tablature();
        }

        $this->tablature->setSong($song);
        $this->tablature->setFormat('gp4');
    }

    /*-------------------------------------------------------------------
    * Private methods are below
    * -----------------------------------------------------------------*/

    /**
     * Reads GraceEffect
     *
     * @param \PhpTabs\Music\NoteEffect $effect
     */
    public function readGrace(NoteEffect $effect): void
    {
        $fret = $this->readUnsignedByte();
        $grace = new EffectGrace();
        $grace->setOnBeat(false);
        $grace->setDead(($fret == 255));
        $grace->setFret(!$grace->isDead() ? $fret : 0);
        $grace->setDynamic((Velocities::MIN_VELOCITY + (Velocities::VELOCITY_INCREMENT * $this->readUnsignedByte())) - Velocities::VELOCITY_INCREMENT);
        $transition = $this->readUnsignedByte();

        if ($transition == 0) {
            $grace->setTransition(EffectGrace::TRANSITION_NONE);
        } elseif ($transition == 1) {
            $grace->setTransition(EffectGrace::TRANSITION_SLIDE);
        } elseif ($transition == 2) {
            $grace->setTransition(EffectGrace::TRANSITION_BEND);
        } elseif ($transition == 3) {
            $grace->setTransition(EffectGrace::TRANSITION_HAMMER);
        }

        $grace->setDuration($this->readUnsignedByte());
        $effect->setGrace($grace);
    }

    /**
     * Loop on measure headers to read
     */
    private function readMeasureHeaders(Song $song, int $count): void
    {
        $timeSignature = new TimeSignature();

        for ($i = 0; $i < $count; $i++) {
            $song->addMeasureHeader($this->factory('GuitarPro3MeasureHeader')->readMeasureHeader(($i + 1), $song, $timeSignature));
        }
    }

    /**
     * Loop on tracks to read
     */
    private function readTracks(Song $song, int $count, array $channels, Lyric $lyric, int $lyricTrack): void
    {
        for ($number = 0; $number < $count; $number++) {
            $track = $this->factory('GuitarPro4Track')->readTrack(
                $song, $channels,
                $number + 1 == $lyricTrack ? $lyric : new Lyric()
            );

            $song->addTrack($track);
        }
    }
}
