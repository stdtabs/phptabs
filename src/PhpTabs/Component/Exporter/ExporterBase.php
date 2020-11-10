<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Exporter;

use PhpTabs\Music\{
    Beat, Chord, Duration, Marker,
    Measure, MeasureHeader, Note,
    TabString, Text, TimeSignature,
    Voice
};

abstract class ExporterBase extends ExporterEffects
{
    /**
     * Export all song as an array
     */
    protected function exportSong(): array
    {
        $content = [
            'name'          => $this->song->getName(),
            'artist'        => $this->song->getArtist(),
            'album'         => $this->song->getAlbum(),
            'author'        => $this->song->getAuthor(),
            'copyright'     => $this->song->getCopyright(),
            'writer'        => $this->song->getWriter(),
            'comments'      => $this->song->getComments(),
            'channels'      => [],
            'measureHeaders'=> [],
            'tracks'        => []
        ];

        $countChannels = $this->song->countChannels();

        for ($i = 0; $i < $countChannels; $i++) {
            $content['channels'][$i] = $this->exportChannel($i);
        }

        $countMeasureHeaders = $this->song->countMeasureHeaders();

        for ($i = 0; $i < $countMeasureHeaders; $i++) {
            $content['measureHeaders'][$i] = $this->exportMeasureHeader(
                $this->song->getMeasureHeader($i)
            );
        }

        $countTracks = $this->song->countTracks();

        for ($i = 0; $i < $countTracks; $i++) {
            $content['tracks'][] = $this->exportTrack($i);
        }

        return ['song' => $content];
    }

    /**
     * @param int $index
     */
    protected function exportTrack(int $index): array
    {
        $track = $this->song->getTrack($index);

        $content = [
            'number'    => $track->getNumber(),
            'offset'    => $track->getOffset(),
            'channelId' => $track->getChannelId(),
            'solo'      => $track->isSolo(),
            'mute'      => $track->isMute(),
            'name'      => $track->getName(),
            'color'     => [
                'R' => $track->getColor()->getR(),
                'G' => $track->getColor()->getG(),
                'B' => $track->getColor()->getB()
            ],
            'lyrics'    => [
                'from'    => $track->getLyrics()->getFrom(),
                'lyrics'  => $track->getLyrics()->getLyrics()
            ],
            'measures'  => [],
            'strings'   => []
        ];

        $countMeasures = $track->countMeasures();

        for ($i = 0; $i < $countMeasures; $i++) {
            $content['measures'][$i] = $this->exportMeasure(
                $track->getMeasure($i),
                $this->song->getMeasureHeader($i)
            );
        }

        $countStrings = $track->countStrings();

        for ($i = 0; $i < $countStrings; $i++) {
            $content['strings'][$i] = $this->exportString($track->getString($i+1));
        }

        return ['track' => $content];
    }

    /**
     * Export a channel as an array
     */
    protected function exportChannel(int $index): array
    {
        $channel = $this->song->getChannel($index);

        $content = [
            'id'        => $channel->getId(),
            'name'      => $channel->getName(),
            'bank'      => $channel->getBank(),
            'program'   => $channel->getProgram(),
            'volume'    => $channel->getVolume(),
            'balance'   => $channel->getBalance(),
            'chorus'    => $channel->getChorus(),
            'reverb'    => $channel->getReverb(),
            'phaser'    => $channel->getPhaser(),
            'tremolo'   => $channel->getTremolo(),
            'parameters'=> []
        ];

        $countParameters = $channel->countParameters();

        for ($i = 0; $i < $countParameters; $i++) {
            $content['parameters'][$i] = [
                'key'   => $channel->getParameter($i)->getKey(),
                'value' => $channel->getParameter($i)->getValue()
            ];
        }

        return ['channel' => $content];
    }

    /**
     * Export a measure as an array
     */
    protected function exportMeasure(Measure $measure, MeasureHeader $measureHeader): array
    {
        $content = [
            'channelId'     => $measure->getTrack()->getChannelId(),
            'clef'          => $measure->getClef(),
            'keySignature'  => $measure->getKeySignature(),
            'header'        => $this->exportMeasureHeader($measureHeader)['header'],
            'beats'         => []
        ];

        $countBeats = $measure->countBeats();

        for ($i = 0; $i < $countBeats; $i++) {
            $content['beats'][$i] = $this->exportBeat($measure->getBeat($i));
        }

        return ['measure' => $content];
    }

    /**
     * Export a beat as an array
     */
    protected function exportBeat(Beat $beat): array
    {
        $content = [
            'start'     => $beat->getStart(),
            'chord'     => $this->exportChord($beat->getChord()),
            'text'      => $this->exportText($beat->getText()),
            'voices'    => [],
            'stroke'    => [
                'direction' => $beat->getStroke()->getDirection(),
                'value'     => $beat->getStroke()->getValue()
            ]
        ];

        $countVoices = $beat->countVoices();

        for ($i = 0; $i < $countVoices; $i++) {
            $content['voices'][$i] = $this->exportVoice($beat->getVoice($i));
        }

        return ['beat' => $content];
    }

    /**
     * Export a voice as an array
     */
    protected function exportVoice(Voice $voice): array
    {
        $content = [
            'duration' => $this->exportDuration($voice->getDuration()),
            'index'    => $voice->getIndex(),
            'empty'    => $voice->isEmpty(),
            'direction'=> $voice->getDirection(),
            'notes'    => []
        ];

        $countNotes = $voice->countNotes();

        for ($i = 0; $i < $countNotes; $i++) {
            $content['notes'][$i] = $this->exportNote($voice->getNote($i));
        }

        return ['voice' => $content];
    }

    /**
     * Export a duration as an array
     */
    protected function exportDuration(Duration $duration): array
    {
        return [
            'value'        => $duration->getValue(),
            'dotted'       => $duration->isDotted(),
            'doubleDotted' => $duration->isDoubleDotted(),
            'divisionType' => [
              'enters'  => $duration->getDivision()->getEnters(),
              'times'   => $duration->getDivision()->getTimes()
            ]
        ];
    }

    /**
     * Export a note as an array
     */
    protected function exportNote(Note $note): array
    {
        return [
            'note' => [
                'value'     => $note->getValue(),
                'velocity'  => $note->getVelocity(),
                'string'    => $note->getString(),
                'tiedNote'  => $note->isTiedNote(),
                'effect'    => $this->exportEffect($note->getEffect())
            ]
        ];
    }

    /**
     * Export a TabString as an array
     */
    protected function exportString(TabString $string): ?array
    {
        return is_object($string)
            ? [
                'string' => [
                    'number'  => $string->getNumber(),
                    'value'   => $string->getValue()
                ]
            ]
            : null;
    }

    /**
     * Export a measure header as an array
     */
    protected function exportMeasureHeader(MeasureHeader $header): array
    {
        return [
            'header' => [
                'number'        => $header->getNumber(),
                'start'         => $header->getStart(),
                'length'        => $header->getLength(),
                'timeSignature' => $this->exportTimeSignature($header->getTimeSignature()),
                'tempo'         => $header->getTempo()->getValue(),
                'marker'        => $this->exportMarker($header->getMarker()),
                'repeatOpen'     => $header->isRepeatOpen(),
                'repeatAlternative' => $header->getRepeatAlternative(),
                'repeatClose'   => $header->getRepeatClose(),
                'tripletFeel'   => $header->getTripletFeel()
            ]
        ];
    }

    /**
     * Export a Chord as an array
     */
    protected function exportChord(Chord $chord = null): ?array
    {
        if (!is_object($chord)) {
            return null;
        }

        $content = [
            'firstFret'  => $chord->getFirstFret(),
            'name'       => $chord->getName(),
            'strings'    => []
        ];

        $countStrings = $chord->countStrings();
        $strings      = $chord->getStrings();

        for ($i = 0; $i < $countStrings; $i++) {
            $content['strings'][] = ['string' => $strings[$i]];
        }

        return $content;
    }

    /**
     * Export a time signature as an array
     */
    protected function exportTimeSignature(TimeSignature $timeSignature): array
    {
        return [
            'numerator'   => $timeSignature->getNumerator(),
            'denominator' => $this->exportDuration($timeSignature->getDenominator())
        ];
    }

    /**
     * Export a marker as an array
     */
    protected function exportMarker(Marker $marker = null): ?array
    {
        return is_object($marker)
            ? [
                'measure' => $marker->getMeasure(),
                'title'   => $marker->getTitle(),
                'color'   => [
                    'R' => $marker->getColor()->getR(),
                    'G' => $marker->getColor()->getG(),
                    'B' => $marker->getColor()->getB()
                ]
            ]
            : null;
    }

    /**
     * Export a text as an array
     */
    protected function exportText(Text $text = null): ?array
    {
        return is_object($text)
            ? ['value' => $text->getValue()]
            : null;
    }
}
