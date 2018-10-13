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

abstract class ExporterBase extends ExporterEffects
{
  /**
   * @return array
   */
  protected function exportSong()
  {
    $content = array(
      'name'          => $this->song->getName(),
      'artist'        => $this->song->getArtist(),
      'album'         => $this->song->getAlbum(),
      'author'        => $this->song->getAuthor(),
      'copyright'     => $this->song->getCopyright(),
      'writer'        => $this->song->getWriter(),
      'comments'      => $this->song->getComments(),
      'channels'      => array(),
      'measureHeaders'=> array(),
      'tracks'        => array()
    );

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
      if (isset($this->filters['trackIndex'])
        && $this->filters['trackIndex'] !== $i
      ) {
        continue;
      }

      $content['tracks'][] = $this->exportTrack($i);
    }

    return array('song' => $content);
  }

  /**
   * @param int $index
   * 
   * @return array
   */
  protected function exportTrack($index)
  {
    $track = $this->song->getTrack($index);

    $content = array(
      'number'    => $track->getNumber(),
      'offset'    => $track->getOffset(),
      'channelId' => $track->getChannelId(),
      'solo'      => $track->isSolo(),
      'mute'      => $track->isMute(),
      'name'      => $track->getName(),
      'color'     => array(
          'R' => $track->getColor()->getR(),
          'G' => $track->getColor()->getG(),
          'B' => $track->getColor()->getB()
      ),
      'lyrics'    => array(
          'from'    => $track->getLyrics()->getFrom(),
          'lyrics'  => $track->getLyrics()->getLyrics()
      ),
      'measures'  => array(),
      'strings'   => array()
    );

    $countMeasures = $track->countMeasures();

    for ($i = 0; $i < $countMeasures; $i++)
    {
      $content['measures'][$i] = $this->exportMeasure(
        $track->getMeasure($i),
        $this->song->getMeasureHeader($i)
      );
    }

    $countStrings = $track->countStrings();

    for ($i = 0; $i < $countStrings; $i++)
    {
      $content['strings'][$i] = $this->exportString($track->getString($i+1));
    }

    return array('track' => $content);
  }

  /**
   * @param int $index
   * 
   * @return array
   */
  protected function exportChannel($index)
  {
    $channel = $this->song->getChannel($index);

    $content = array(
      'channelId' => $channel->getChannelId(),
      'name'      => $channel->getName(),
      'bank'      => $channel->getBank(),
      'program'   => $channel->getProgram(),
      'volume'    => $channel->getVolume(),
      'balance'   => $channel->getBalance(),
      'chorus'    => $channel->getChorus(),
      'reverb'    => $channel->getReverb(),
      'phaser'    => $channel->getPhaser(),
      'tremolo'   => $channel->getTremolo(),
      'parameters'=> array()
    );

    $countParameters = $channel->countParameters();

    for ($i = 0; $i < $countParameters; $i++)
    {
      $content['parameters'][$i] = array(
        'key'   => $channel->getParameter($i)->getKey(),
        'value' => $channel->getParameter($i)->getValue()
      );
    }

    return array('channel' => $content);
  }

  /**
   * @param \PhpTabs\Music\Measure $measure
   * @param \PhpTabs\Music\MeasureHeader $measureHeader
   * 
   * @return array
   */
  protected function exportMeasure($measure, $measureHeader)
  {
    $content = array(
      'channelId'     => $measure->getTrack()->getChannelId(),
      'clef'          => $measure->getClef(),
      'keySignature'  => $measure->getKeySignature(),
      'header'        => $this->exportMeasureHeader($measureHeader)['header'],
      'beats'         => array()
    );

    $countBeats = $measure->countBeats();

    for ($i = 0; $i < $countBeats; $i++)
    {
      $content['beats'][$i] = $this->exportBeat($measure->getBeat($i));
    }

    return array('measure' => $content);
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
   * 
   * @return array
   */
  protected function exportBeat($beat)
  {
    $content = array(
      'start'     => $beat->getStart(),
      'chord'     => $this->exportChord($beat->getChord()),
      'text'      => $this->exportText($beat->getText()),
      'voices'    => array(),
      'stroke'    => array(
        'direction' => $beat->getStroke()->getDirection(),
        'value'     => $beat->getStroke()->getValue()
      )
    );

    $countVoices = $beat->countVoices();

    for ($i = 0; $i < $countVoices; $i++)
    {
      $content['voices'][$i] = $this->exportVoice($beat->getVoice($i));
    }

    return array('beat' => $content);
  }

  /**
   * @param \PhpTabs\Music\Voice $voice
   * 
   * @return array
   */
  protected function exportVoice($voice)
  {
    $content = array(
      'duration' => $this->exportDuration($voice->getDuration()),
      'index'    => $voice->getIndex(),
      'empty'    => $voice->isEmpty(),
      'direction'=> $voice->getDirection(),
      'notes'    => array()
    );

    $countNotes = $voice->countNotes();

    for ($i = 0; $i < $countNotes; $i++)
    {
      $content['notes'][$i] = $this->exportNote($voice->getNote($i));
    }

    return array('voice' => $content);
  }

  /**
   * @param \PhpTabs\Music\Duration $duration
   * 
   * @return array
   */
  protected function exportDuration($duration)
  {
    return array(
        'value'        => $duration->getValue(),
        'dotted'       => $duration->isDotted(),
        'doubleDotted' => $duration->isDoubleDotted(),
        'divisionType' => array(
          'enters'  => $duration->getDivision()->getEnters(),
          'times'   => $duration->getDivision()->getTimes()
        )
    );
  }

  /**
   * @param \PhpTabs\Music\Note $note
   * 
   * @return array
   */
  protected function exportNote($note)
  {
    return array('note' => 
      array(
        'value'     => $note->getValue(),
        'velocity'  => $note->getVelocity(),
        'string'    => $note->getString(),
        'tiedNote'  => $note->isTiedNote(),
        'effect'    => $this->exportEffect($note->getEffect())
      )
    );
  }

  /**
   * @param \PhpTabs\Music\TabString $string
   * 
   * @return array
   */
  protected function exportString($string)
  {
    return is_object($string) ? array('string' => array(
      'number'  => $string->getNumber(),
      'value'   => $string->getValue()
    )) : null;
  }

  /**
   * @param \PhpTabs\Music\MeasureHeader $header
   * 
   * @return array
   */
  protected function exportMeasureHeader($header)
  {
    return array('header' => array(
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
    ));
  }

  /**
   * @param \PhpTabs\Music\Chord $chord
   * 
   * @return array
   */
  protected function exportChord($chord)
  {
    if (!is_object($chord))
    {
      return null;
    }

    $content = array(
      'firstFret'  => $chord->getFirstFret(),
      'name'       => $chord->getName(),
      'strings'    => array()
    );

    $countStrings = $chord->countStrings();
    $strings = $chord->getStrings();

    for ($i = 0; $i < $countStrings; $i++)
    {
      $content['strings'][] = array('string' => $strings[$i]);
    }

    return $content;
  }

  /**
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   * 
   * @return array
   */
  protected function exportTimeSignature($timeSignature)
  {
    return array(
      'numerator'   => $timeSignature->getNumerator(), 
      'denominator' => $this->exportDuration($timeSignature->getDenominator())
    );
  }

  /**
   * @param \PhpTabs\Music\Marker $marker
   * 
   * @return array
   */
  protected function exportMarker($marker)
  {
    return is_object($marker) ? array(
      'measure' => $marker->getMeasure(), 
      'title'   => $marker->getTitle(),
      'color'   => array(
        'R' => $marker->getColor()->getR(),
        'G' => $marker->getColor()->getG(),
        'B' => $marker->getColor()->getB()
      )
    ) : null;
  }

  /**
   * @param \PhpTabs\Music\Text $text
   * 
   * @return array
   */
  protected function exportText($text)
  {
    return is_object($text) ? array(
      'value' => $text->getValue()
    ) : null;
  }
}
