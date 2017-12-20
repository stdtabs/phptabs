<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use Exception;
use PhpTabs\Music\Beat;
use PhpTabs\Music\Channel;
use PhpTabs\Music\ChannelParameter;
use PhpTabs\Music\Chord;
use PhpTabs\Music\Color;
use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectGrace;
use PhpTabs\Music\EffectHarmonic;
use PhpTabs\Music\EffectTremoloBar;
use PhpTabs\Music\EffectTremoloPicking;
use PhpTabs\Music\EffectTrill;
use PhpTabs\Music\Lyric;
use PhpTabs\Music\Marker;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Note;
use PhpTabs\Music\NoteEffect;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Text;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\Music\Voice;

abstract class ImporterBase
{
  /**
   * Parse song level
   * 
   * @param array $data
   */
  protected function parseSong(array $data)
  {
    $this->checkKeys($data, [
        'name',
        'artist',
        'album',
        'author',
        'copyright',
        'writer',
        'comments',
        'channels',
        'measureHeaders',
        'tracks'
    ]);

    $this->song->setName($data['name']);
    $this->song->setArtist($data['artist']);
    $this->song->setAlbum($data['album']);
    $this->song->setAuthor($data['author']);
    $this->song->setCopyright($data['copyright']);
    $this->song->setWriter($data['writer']);
    $this->song->setComments($data['comments']);

    $channelCount = count($data['channels']);

    foreach ($data['channels'] as $channel) {
      $this->checkKeys($channel, 'channel');
      $this->song->addChannel(
        $this->parseChannel($channel['channel'])
      );
    }

    foreach ($data['measureHeaders'] as $header) {
      $this->checkKeys($header, 'header');
      $this->song->addMeasureHeader(
        $this->parseMeasureHeader($header['header'])
      );
    }

    foreach ($data['tracks'] as $track) {
      $this->checkKeys($track, 'track');
      $this->song->addTrack(
        $this->parseTrack($track['track'])
      );
    }
  }

  /**
   * Parse a track array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Track
   */
  protected function parseTrack(array $data)
  {
    $this->checkKeys($data, [
      'number',
      'offset',
      'channelId',
      'solo',
      'mute',
      'name',
      'color',
      'lyrics',
      'measures',
      'strings'
    ]);

    $track = new Track();
    $track->setNumber($data['number']);
    $track->setOffset($data['offset']);
    $track->setChannelId($data['channelId']);
    $track->setSolo($data['solo']);
    $track->setMute($data['mute']);
    $track->setName($data['name']);

    $track->setColor(
      $this->parseColor($data['color'])
    );

    $track->setLyrics(
      $this->parseLyrics($data['lyrics'])
    );

    foreach ($data['measures'] as $index => $item) {
      $this->checkKeys($item, 'measure');
      $track->addMeasure(
        $this->parseMeasure(
          $item['measure'],
          $this->song->getMeasureHeader($index)
        )
      );
    }

    foreach ($data['strings'] as $string) {
      $this->checkKeys($string, 'string');
      $track->addString(
        $this->parseString($string['string'])
      );
    }

    $track->setSong($this->song);

    return $track;
  }

  /**
   * Parse a string array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\TabString
   */
  protected function parseString(array $data)
  {
    $this->checkKeys($data, ['number', 'value']);

    return new TabString($data['number'], $data['value']);
  }

  /**
   * Parse a measure array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Measure
   */
  protected function parseMeasure(array $data, MeasureHeader $header)
  {
    $this->checkKeys(
      $data,
      ['clef', 'keySignature', 'beats']
    );

    $measure = new Measure($header);
    $measure->setClef($data['clef']);
    $measure->setKeySignature($data['keySignature']);

    foreach ($data['beats'] as $beat) {
      $this->checkKeys($beat, 'beat');
      $measure->addBeat(
        $this->parseBeat($beat['beat'])
      );
    }

    return $measure;
  }

  /**
   * Parse a beat array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Beat
   */
  protected function parseBeat(array $data)
  {
    $this->checkKeys($data, ['start', 'voices', 'stroke']);

    $beat = new Beat();
    $beat->setStart($data['start']);
    
    if (isset($data['chord'])) {
      $beat->setChord(
        $this->parseChord($data['chord'])
      );
    }

    if (isset($data['text'])) {
      $beat->setText(
        $this->parseText($data['text'])
      );
    }

    $this->checkKeys($data['stroke'], ['direction', 'value']);
    $beat->getStroke()->setDirection($data['stroke']['direction']);
    $beat->getStroke()->setValue($data['stroke']['value']);

    foreach ($data['voices'] as $index => $voice) {
      $this->checkKeys($voice, 'voice');
      $beat->setVoice(
        $index,
        $this->parseVoice($index, $voice['voice'])
      );
    }
  
    return $beat;
  }

  /**
   * Parse a chord array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Chord
   */
  protected function parseChord(array $data)
  {
    $this->checkKeys($data, ['firstFret', 'name', 'strings']);

    $chord = new Chord(count($data['strings']));
    $chord->setName($data['name']);
    $chord->setFirstFret($data['firstFret']);

    foreach ($data['strings'] as $index => $string) {
      $this->checkKeys($string, 'string');
      $chord->addFretValue($index, $string['string']);
    }

    return $chord;
  }

  /**
   * Parse a text array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Text
   */
  protected function parseText(array $data)
  {
    $this->checkKeys($data, 'value');

    $text = new Text();
    $text->setValue($data['value']);
    return $text;
  }

  /**
   * Parse a voice array
   * 
   * @param  int   $index
   * @param  array $data
   * @return \PhpTabs\Music\Voice
   */
  protected function parseVoice($index, array $data)
  {
    $this->checkKeys(
      $data,
      ['duration', 'index', 'empty', 'direction', 'notes']
    );

    $voice = new Voice($index);

    $voice->setDuration(
      $this->parseDuration($data['duration'])
    );

    $voice->setDirection($data['direction']);

    foreach ($data['notes'] as $note) {
      $this->checkKeys($note, 'note');
      $voice->addNote(
        $this->parseNote($note['note'])
      );
    }

    $voice->setEmpty($data['empty']);

    return $voice;
  }

  /**
   * Parse a note array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Note
   */
  protected function parseNote(array $data)
  {
    $this->checkKeys(
      $data,
      ['value', 'velocity', 'string', 'tiedNote', 'effect']
    );

    $note = new Note();
    $note->setValue($data['value']);
    $note->setVelocity($data['velocity']);
    $note->setString($data['string']);
    $note->setTiedNote($data['tiedNote']);

    $note->setEffect(
      $this->parseNoteEffect($data['effect'])
    );

    return $note;
  }

  /**
   * Parse effect array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\NoteEffect
   */
  protected function parseNoteEffect(array $data)
  {
    $this->checkKeys(
      $data, [
        'bend',
        'tremoloBar',
        'harmonic',
        'grace',
        'trill',
        'tremoloPicking',
        'vibrato',
        'deadNote',
        'slide',
        'hammer',
        'ghostNote',
        'accentuatedNote',
        'heavyAccentuatedNote',
        'palmMute',
        'staccato',
        'tapping',
        'slapping',
        'popping',
        'fadeIn',
        'letRing'
    ]);

    $effect = new NoteEffect();

    if ($data['bend'] !== null) {
      $effect->setBend(
        $this->parseEffectPoints($data['bend'], new EffectBend())
      );
    }

    if ($data['tremoloBar'] !== null) {
      $effect->setTremoloBar(
        $this->parseEffectPoints($data['tremoloBar'], new EffectTremoloBar())
      );
    }

    if ($data['harmonic'] !== null) {
      $effect->setHarmonic(
        $this->parseHarmonic($data['harmonic'])
      );
    }

    if ($data['grace'] !== null) {
      $effect->setGrace(
        $this->parseGrace($data['grace'])
      );
    }

    if ($data['trill'] !== null) {
      $effect->setTrill(
        $this->parseTrill($data['trill'])
      );
    }

    if ($data['tremoloPicking'] !== null) {
      $effect->setTremoloPicking(
        $this->parseTremoloPicking($data['tremoloPicking'])
      );
    }

    foreach ([
        'vibrato',
        'deadNote',
        'slide',
        'hammer',
        'ghostNote',
        'accentuatedNote',
        'heavyAccentuatedNote',
        'palmMute',
        'staccato',
        'tapping',
        'slapping',
        'popping',
        'fadeIn',
        'letRing'] as $key
    ) {

      if ($data[$key] !== null) {
        $method = 'set' . ucfirst($key);
        $effect->$method($data[$key]);
      }
    }

    return $effect;
  }

  /**
   * Parse a trill array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\EffectTrill
   */
  protected function parseTrill(array $data)
  {
    $this->checkKeys($data, ['fret', 'duration']);

    $effect = new EffectTrill();
    $effect->setFret($data['fret']);
    $effect->setDuration(
      $this->parseDuration($data['duration'])
    );

    return $effect;
  }

  /**
   * Parse a tremolo picking array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\EffectTremoloPicking
   */
  protected function parseTremoloPicking(array $data)
  {
    $this->checkKeys($data, 'duration');

    $effect = new EffectTremoloPicking();
    $effect->setDuration(
      $this->parseDuration($data['duration'])
    );

    return $effect;
  }

  /**
   * Parse a grace array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\EffectGrace
   */
  protected function parseGrace(array $data)
  { 
    $this->checkKeys($data, [
      'fret',
      'duration',
      'dynamic',
      'transition',
      'onBeat',
      'dead'
    ]);

    $grace = new EffectGrace();
    $grace->setFret($data['fret']);
    $grace->setDuration($data['duration']);
    $grace->setDynamic($data['dynamic']);
    $grace->setTransition($data['transition']);
    $grace->setOnBeat($data['onBeat']);
    $grace->setDead($data['dead']);

    return $grace;
  }

  /**
   * Parse an harmonic array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\EffectHarmonic
   */
  protected function parseHarmonic(array $data)
  {
    $this->checkKeys($data, [
      'type',
      'data',
      'isNatural',
      'isArtificial',
      'isTapped',
      'isPinch',
      'isSemi'
    ]);

    $harmonic = new EffectHarmonic();
    $harmonic->setType($data['type']);
    $harmonic->setData($data['data']);

    return $harmonic;
  }

  /**
   * Parse a bend array
   * 
   * @param  array $data
   * @param  \PhpTabs\Music\Bend|\PhpTabs\Music\TremoloBar $effect
   * @return \PhpTabs\Music\Bend|\PhpTabs\Music\TremoloBar
   */
  protected function parseEffectPoints(array $data, $effect)
  {
    $this->checkKeys($data, 'points');

    foreach ($data['points'] as $point) {
      $this->checkKeys($point, ['position', 'value']);
      $effect->addPoint($point['position'], $point['value']);
    }

    return $effect;
  }

  /**
   * Parse a lyrics array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Lyric
   */
  protected function parseLyrics(array $data)
  {
    $this->checkKeys($data, ['from', 'lyrics']);

    $lyric = new Lyric();
    $lyric->setFrom($data['from']);
    $lyric->setLyrics($data['lyrics']);

    return $lyric;
  }

  /**
   * Parse a measure header array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\MeasureHeader
   */
  protected function parseMeasureHeader(array $data)
  {
    $this->checkKeys($data, [
      'number',
      'start',
      'timeSignature',
      'tempo',
      'repeatOpen',
      'repeatAlternative',
      'repeatClose',
      'tripletFeel'
    ]);

    $header = new MeasureHeader();
    $header->setNumber($data['number']);
    $header->setStart($data['start']);

    $header->setTimeSignature(
      $this->parseTimeSignature($data['timeSignature'])
    );

    $header->setTempo(
      $this->parseTempo($data['tempo'])
    );

    if (isset($data['marker'])) {
      $header->setMarker(
        $this->parseMarker($data['marker'])
      );
    }

    $header->setRepeatOpen($data['repeatOpen']);
    $header->setRepeatAlternative($data['repeatAlternative']);
    $header->setRepeatClose($data['repeatClose']);
    $header->setTripletFeel($data['tripletFeel']);

    return $header;
  }

  /**
   * Parse a marker array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Marker
   */
  protected function parseMarker(array $data)
  {
    $this->checkKeys($data, ['measure', 'title', 'color']);

    $marker = new Marker();
    $marker->setMeasure($data['measure']);
    $marker->setTitle($data['title']);
    $marker->setColor(
      $this->parseColor($data['color'])
    );

    return $marker;
  }


  /**
   * Parse a color array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Color
   */
  protected function parseColor(array $data)
  {
    $this->checkKeys($data, ['R', 'G', 'B']);

    $color = new Color();
    $color->setR($data['R']);
    $color->setG($data['G']);
    $color->setB($data['B']);

    return $color;
  }

  /**
   * Parse a tempo value
   * 
   * @param  int $data
   * @return \PhpTabs\Music\Tempo
   */
  protected function parseTempo($data)
  {
    $tempo = new Tempo();
    $tempo->setValue($data);

    return $tempo;
  }

  /**
   * Parse a time signature array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\TimeSignature
   */
  protected function parseTimeSignature(array $data)
  {
    $this->checkKeys($data, ['numerator', 'denominator']);

    $timeSignature = new TimeSignature();
    $timeSignature->setNumerator($data['numerator']);
    $timeSignature->setDenominator(
      $this->parseDuration($data['denominator'])
    );

    return $timeSignature;
  }

  /**
   * Parse a duration array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Duration
   */
  protected function parseDuration(array $data)
  {
    $this->checkKeys($data, ['value', 'dotted', 'doubleDotted', 'divisionType']);
    $this->checkKeys($data['divisionType'], ['enters', 'times']);

    $duration = new Duration();
    $duration->setValue($data['value']);
    $duration->setDotted($data['dotted']);
    $duration->setDoubleDotted($data['doubleDotted']);
    $duration->getDivision()->setEnters($data['divisionType']['enters']);
    $duration->getDivision()->setTimes($data['divisionType']['times']);

    return $duration;
  }

  /**
   * Parse a channel array
   * 
   * @param  array $data
   * @return \PhpTabs\Music\Channel
   */
  protected function parseChannel(array $data)
  {
    $this->checkKeys($data, [
      'channelId',
      'name',
      'bank',
      'program',
      'volume',
      'balance',
      'chorus',
      'reverb',
      'phaser',
      'tremolo',
      'parameters'
    ]);

    $channel = new Channel();
    $channel->setChannelId($data['channelId']);
    $channel->setName($data['name']);
    $channel->setBank($data['bank']);
    $channel->setProgram($data['program']);
    $channel->setVolume($data['volume']);
    $channel->setBalance($data['balance']);
    $channel->setChorus($data['chorus']);
    $channel->setReverb($data['reverb']);
    $channel->setPhaser($data['phaser']);
    $channel->setTremolo($data['tremolo']);

    foreach ($data['parameters'] as $parameter) {
      $channel->addParameter(
        $this->parseChannelParameter($parameter)
      );
    }

    return $channel;
  }

  /**
   * Parse a channel parameter
   * 
   * @param array $parameter
   * @return \PhpTabs\Music\ChannelParameter
   */
  protected function parseChannelParameter(array $data)
  {
    $this->checkKeys($data, ['key', 'value']);

    $parameter = new ChannelParameter();
    $parameter->setKey($data['key']);
    $parameter->setValue($data['value']);

    return $parameter;
  }

  /**
   * Check that a key is set in a data array
   * 
   * @param  array        $data
   * @param  array|string $keys
   * @throws \Exception if a key is not defined
   */
  protected function checkKeys(array $data, $keys)
  {
    if (is_array($keys)) {
      foreach ($keys as $key) {
        if (!isset($data[$key]) && !array_key_exists($key, $data)) {
          throw new Exception ("Invalid data: '$key' key must be set");
        }
      }
    } elseif (is_string($keys)) {
      if (!isset($data[$keys]) && !array_key_exists($key, $data)) {
        throw new Exception ("Invalid data: '$keys' key must be set");
      }
    }
  }
}
