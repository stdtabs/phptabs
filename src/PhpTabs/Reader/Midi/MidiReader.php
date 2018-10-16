<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\Midi;

use Exception;
use PhpTabs\Component\Config;
use PhpTabs\Component\Log;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\Beat;
use PhpTabs\Music\Channel;
use PhpTabs\Music\Color;
use PhpTabs\Music\Duration;
use PhpTabs\Music\EffectBend;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Note;
use PhpTabs\Music\Song;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\Share\ChannelRoute;
use PhpTabs\Share\ChannelRouter;

class MidiReader extends MidiReaderBase
{
  const CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX = true;
  const STATUS_NONE = 0;
  const STATUS_ONE_BYTE = 1;
  const STATUS_TWO_BYTES = 2;
  const STATUS_SYSEX = 3;
  const STATUS_META = 4;

  /** @var integer resolution */
  private $resolution;
  private $channels;
  private $headers;
  private $tracks;
  private $tempNotes;
  private $tempChannels;
  private $trackTuningHelpers;
  private $settings;

  /**
   * @param \PhpTabs\Component\File $file An input file to read
   */
  public function __construct(File $file)
  {
    parent::__construct($file);

    $song = new Song();

    $this->setTablature($song);

    $this->settings = (new MidiSettings())->getDefaults();
    $this->sequence = $this->getSequence();
    $this->initFields($this->sequence);

    $countTracks = $this->sequence->countTracks();

    for ($i = 0; $i < $countTracks; $i++) {

      $track       = $this->sequence->getTrack($i);
      $trackNumber = $this->getNextTrackNumber();
      $events      = $track->countEvents();

      for ($j = 0; $j < $events; $j++) {
        $event = $track->get($j);

        $this->parseMessage($trackNumber, $event->getTick(), $event->getMessage());
      }
    }

    $this->checkAll();

    array_walk($this->channels, function($channel) use (&$song) {
      $song->addChannel($channel);
    });

    array_walk($this->headers, function($header) use (&$song) {
      $song->addMeasureHeader($header);
    });

    array_walk($this->tracks, function($track) use (&$song) {
      $song->addTrack($track);
    });

    $this->adjust($song);

    $this->closeStream();
  }

  /**
   * {@inheritdoc}
   */
  public function getTablature()
  {
    return isset($this->tablature)
      ? $this->tablature 
      : new Tablature();
  }

  /**
   * Initializes Tablature with read Song
   * 
   * @param \PhpTabs\Music\Song $song as read from file
   */
  private function setTablature(Song $song)
  {
    if (!isset($this->tablature)) {
      $this->tablature = new Tablature();
    }

    $this->tablature->setSong($song);
    $this->tablature->setFormat('mid');
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function adjust(Song $song)
  {
    (new MidiAdjuster($song))->adjustSong();
  }

  private function checkAll()
  {
    $this->checkChannels();
    $this->checkTracks();

    $headerCount = count($this->headers);
    $trackCount  = count($this->tracks);

    for ($i = 0; $i < $trackCount; $i++) {

      $track = $this->tracks[$i];
      $track->setSong($this->tablature->getSong());

      while ($track->countMeasures() < $headerCount) {

        $start = Duration::QUARTER_TIME;

        $lastMeasure = $track->countMeasures() > 0
            ? $track->getMeasure($track->countMeasures() - 1) : null;

        if ($lastMeasure !== null) {
          $start = $lastMeasure->getStart() + $lastMeasure->getLength();
        }

        $track->addMeasure(new Measure($this->getHeader($start)));
      }
    }

    if (!count($this->headers) || !count($this->tracks)) {
      throw new Exception('Empty Song');
    }
  }

  private function checkChannels()
  {
    for ($tc = 0; $tc < count($this->tempChannels); $tc++)
    {
      $tempChannel = $this->tempChannels[$tc];

      if ($tempChannel->getTrack() > 0)
      {
        $channelExists = false;

        for ($c = 0; $c < count($this->channels); $c++)
        {
          $channel = $this->channels[$c];
          $channelRoute = $this->channelRouter->getRoute($channel->getChannelId());
          if ($channelRoute !== null)
          {
            if ($channelRoute->getChannel1() == $tempChannel->getChannel() 
              || $channelRoute->getChannel2() == $tempChannel->getChannel())
            {
              $channelExists = true;
            }
          }
        }

        if (!$channelExists)
        {
          $channel = new Channel();
          $channel->setChannelId(count($this->channels) + 1);
          $channel->setProgram($tempChannel->getInstrument());
          $channel->setVolume($tempChannel->getVolume());
          $channel->setBalance($tempChannel->getBalance());
          $channel->setName(('#' . $channel->getChannelId()));
          $channel->setBank($tempChannel->getChannel() == 9
            ? Channel::DEFAULT_PERCUSSION_BANK : Channel::DEFAULT_BANK);

          $channelRoute = new ChannelRoute($channel->getChannelId());
          $channelRoute->setChannel1($tempChannel->getChannel());
          $channelRoute->setChannel2($tempChannel->getChannel());

          for ($tcAux = ($tc + 1); $tcAux < count($this->tempChannels); $tcAux++)
          {
            $tempChannelAux = $this->tempChannels[$tcAux];

            if ($tempChannel->getTrack() == $tempChannelAux->getTrack())
            {
              if ($channelRoute->getChannel2() == $channelRoute->getChannel1())
              {
                $channelRoute->setChannel2($tempChannelAux->getChannel());
              }
              else
              {
                $tempChannelAux->setTrack(-1);
              }
            }
          }

          $this->channelRouter->configureRoutes($channelRoute, ($tempChannel->getChannel() == 9));
          $this->channels[] = $channel;
        }
      }
    }
  }

  private function checkTracks()
  {
    array_walk($this->tracks, function($track) {
      $trackChannel = null;

      array_walk($this->tempChannels, function($tempChannel) use (&$trackChannel, $track) {
        if ($tempChannel->getTrack() == $track->getNumber())
        {
          array_walk($this->channels, function($channel) use (&$tempChannel, &$trackChannel) {
            $channelRoute = $this->channelRouter->getRoute($channel->getChannelId());

            if ($channelRoute !== null && $tempChannel->getChannel() == $channelRoute->getChannel1())
            {
              $trackChannel = $channel;
            }
          });
        }
      });

      if ($trackChannel !== null)
      {
        $track->setChannelId($trackChannel->getChannelId());
      }

      if ($trackChannel !== null && $trackChannel->isPercussionChannel())
      {
        $track->setStrings($this->createPercussionStrings(6)); 
      }
      else
      {
        $track->setStrings($this->getTrackTuningHelper($track->getNumber())->getStrings());
      }
    });
  }

  /**
   * Create percussion strings
   * 
   * @param  int $stringCount
   * @return array
   */
  private function createPercussionStrings($stringCount)
  {
    $strings = array();

    for ($i = 1; $i <= $stringCount; $i++) {
      $strings[] = new TabString($i, 0);
    }

    return $strings;
  }

  /**
   * @param mixed $tick
   * 
   * @return \PhpTabs\Music\MeasureHeader
   */
  private function getHeader($tick)
  {
    $realTick = $tick >= Duration::QUARTER_TIME
      ? $tick : Duration::QUARTER_TIME;

    foreach ($this->headers as $header)
    {
      if ($realTick >= $header->getStart()
        && $realTick < ($header->getStart() + $header->getLength()))
      {
        return $header;
      }
    }

    $last = $this->getLastHeader();
    $header = new MeasureHeader();

    $header->setNumber($last !== null
        ? $last->getNumber() + 1 : 1
    );

    $header->setStart(
      $last !== null 
            ? ($last->getStart() + $last->getLength())
            : Duration::QUARTER_TIME
    );

    $header->getTempo()->setValue($last !== null
        ? $last->getTempo()->getValue() : 120
    );

    if ($last !== null) {
      $header->getTimeSignature()->copyFrom($last->getTimeSignature());
    } else {
      $header->getTimeSignature()->setNumerator(4);
      $header->getTimeSignature()->getDenominator()->setValue(Duration::QUARTER);
    }

    $this->headers[] = $header;

    if ( $realTick >= $header->getStart() 
      && $realTick < ($header->getStart() + $header->getLength()))
    {
      return $header;
    }

    return $this->getHeader($realTick);
  }

  /**
   * @return null|\PhpTabs\Music\MeasureHeader
   */
  private function getLastHeader()
  {
    if (count($this->headers))
    {
      return $this->headers[count($this->headers) - 1];
    }

    return null;
  }

  /**
   * @param \PhpTabs\Music\Track $track
   * @param mixed $tick
   * 
   * @return \PhpTabs\Music\Measure
   */
  private function getMeasure(Track $track, $tick)
  {
    $realTick = $tick >= Duration::QUARTER_TIME
      ? $tick : Duration::QUARTER_TIME;

    $measures = $track->getMeasures();

    foreach ($measures as $measure)
    {
      if ($realTick >= $measure->getStart() && $realTick < $measure->getStart() + $measure->getLength())
      {
        return $measure;
      }
    }

    $this->getHeader($realTick);

    for ($i = 0; $i < count($this->headers); $i++)
    {
      $exist = false;
      $header = $this->headers[$i];
      $measureCount = $track->countMeasures();

      for ($j = 0; $j < $measureCount; $j++)
      {
        $measure = $track->getMeasure($j);

        if ($measure->getHeader() == $header)
        {
          $exist = true;
        }
      }

      if (!$exist)
      {
        $measure = new Measure($header);
        $track->addMeasure($measure);
      }
    }

    return $this->getMeasure($track, $realTick);
  }

  /**
   * @return int
   */
  private function getNextTrackNumber()
  {
    return count($this->tracks) + 1;
  }

  /**
   * @param integer $channel
   * 
   * @return \PhpTabs\Reader\Midi\MidiChannel
   */
  public function getTempChannel($channel)
  {
    foreach ($this->tempChannels as $tempChannel) {
      if ($tempChannel->getChannel() == $channel) {
        return $tempChannel;
      }
    }

    $tempChannel = new MidiChannel($channel);
    $this->tempChannels[] = $tempChannel;

    return $tempChannel;
  }

  /**
   * @param int   $track
   * @param int   $channel
   * @param mixed $value
   * @param bool  $purge
   * 
   * @return null|\PhpTabs\Reader\Midi\MidiNote
   */
  private function getTempNote($track, $channel, $value, $purge)
  {
    $countTempNotes = count($this->tempNotes);

    for ($i = 0; $i < $countTempNotes; $i++) {
      $note = $this->tempNotes[$i];

      if ( $note->getTrack()    == $track
        && $note->getChannel()  == $channel 
        && $note->getValue()    == $value
      ) {
        if ($purge) {
          array_splice($this->tempNotes, $i, 1);
        }

        return $note;
      }
    }

    return null;
  }

  /**
   * @param int $track
   * 
   * @return \PhpTabs\Reader\Midi\MidiTrackTuningHelper
   */
  private function getTrackTuningHelper($track)
  {
    foreach ($this->trackTuningHelpers as $helper) {
      if ($helper->getTrack() == $track) {
        return $helper;
      }
    }

    $helper = new MidiTrackTuningHelper($track);
    $this->trackTuningHelpers[] = $helper;

    return $helper;
  }

  /**
   * @param int $trackNumber
   * @param mixed $tick
   * @param \PhpTabs\Reader\Midi\MidiMessage $message
   */
  private function parseMessage($trackNumber, $tick, MidiMessage $message)
  {
    $parsedTick = $this->parseTick($tick + $this->resolution);

    //NOTE ON
    if ($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::NOTE_ON)
    {
      $this->parseNoteOn($trackNumber, $parsedTick, $message->getData());
    }
    //NOTE OFF
    elseif ($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::NOTE_OFF)
    {
      $this->parseNoteOff($trackNumber, $parsedTick, $message->getData());
    }
    //PROGRAM CHANGE
    elseif ($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::PROGRAM_CHANGE)
    {
      $this->parseProgramChange($message->getData());
    }
    //CONTROL CHANGE
    elseif ($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::CONTROL_CHANGE)
    {
      $this->parseControlChange($message->getData());
    }
    //PITCH BEND
    elseif ($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::PITCH_BEND)
    {
      $this->parsePitchBend($message->getData());
    }
    //TIME SIGNATURE
    elseif ($message->getType() == MidiMessage::TYPE_META && $message->getCommand() == MidiMessage::TIME_SIGNATURE_CHANGE)
    {
      $this->parseTimeSignature($parsedTick, $message->getData());
    }
    //TEMPO
    elseif ($message->getType() == MidiMessage::TYPE_META && $message->getCommand() == MidiMessage::TEMPO_CHANGE)
    {
      $this->parseTempo($parsedTick, $message->getData());
    }
    // SKIPPED MESSAGES (Undefined commands)
    else
    {
      $logMessage = sprintf('track=%d, tick=%d, type=%s, command=%x, data=array(%s)'
        , $trackNumber
        , $tick
        , $message->getType()
        , $message->getCommand()
        , implode(', ', $message->getData())
      );

      Log::add($logMessage, 'MIDI_SKIPPED_MESSAGE');
    }
  }

  /**
   * @return \PhpTabs\Reader\Midi\MidiSequence $sequence
   */
  private function getSequence()
  {
    if ($this->readInt() != MidiReaderInterface::HEADER_MAGIC) {
      throw new Exception('Not a MIDI file: wrong header magic');
    }

    $headerLength = $this->readInt();

    if ($headerLength < MidiReaderInterface::HEADER_LENGTH) {
      throw new Exception('Corrupted MIDI file: wrong header length');
    }

    $type = $this->readShort();

    if ($type < 0 || $type > 2) {
      throw new Exception('Corrupted MIDI file: illegal type');
    }

    if ($type == 2) {
      throw new Exception('this implementation doesn\'t support type 2 MIDI files');
    }

    $trackCount = $this->readShort();

    if ($trackCount <= 0) {
      throw new Exception('Corrupted MIDI file: number of tracks must be positive');
    }

    if ($type == 0 && $trackCount != 1) {
      throw new Exception("Corrupted MIDI file:  type 0 files must contain exactly one track $trackCount");
    }

    $divisionType = -1.0;
    $resolution = -1;
    $division = $this->readUnsignedShort();

    if (($division & 0x8000) != 0) {
      $frameType = -(($division >> 8) & 0xff);

      switch ($frameType) {
        case 24:
          $divisionType = MidiReaderInterface::SMPTE_24;
          break;
        case 25:
          $divisionType = MidiReaderInterface::SMPTE_25;
          break;
        case 29:
          $divisionType = MidiReaderInterface::SMPTE_30DROP;
          break;
        case 30:
          $divisionType = MidiReaderInterface::SMPTE_30;
          break;
        default:
          throw new Exception('Corrupted MIDI file: illegal frame division type');
          break;
      }

      $resolution = $division & 0xff;
    } else {
      $divisionType = MidiReaderInterface::PPQ;
      $resolution = $division & 0x7fff;
    }

    $this->skip($headerLength - MidiReaderInterface::HEADER_LENGTH);

    $sequence = new MidiSequence($divisionType, $resolution);

    for ($i = 0; $i < $trackCount; $i++) {
      $track = new MidiTrack();
      $sequence->addTrack($track);
      $this->readTrack($track);
    }

    return $sequence;
  }

  /**
   * @param  int $number
   * @return \PhpTabs\Music\Track
   */
  private function getTrack($number)
  {
    foreach ($this->tracks as $track) {
      if ($track->getNumber() == $number) {
        return $track;
      }
    }

    $track = new Track();
    $track->setNumber($number);
    $track->setChannelId(-1);
    $track->getColor()->setR(Color::$red[0]);
    $track->getColor()->setG(Color::$red[1]);
    $track->getColor()->setB(Color::$red[2]);

    $this->tracks[] = $track;

    return $track;
  }

  /**
   * @param mixed $statusByte
   * 
   * @return mixed
   */
  private function getType($statusByte)
  {
    if ($statusByte < 0xf0) {
      $command = $statusByte & 0xf0;

      switch ($command) {
        case 0x80:
        case 0x90:
        case 0xa0:
        case 0xb0:
        case 0xe0:
          return MidiReader::STATUS_TWO_BYTES;
        case 0xc0:
        case 0xd0:
          return MidiReader::STATUS_ONE_BYTE;
      }
    }

    switch ($statusByte) {
      case 0xf0:
      case 0xf7:
        return MidiReader::STATUS_SYSEX;
      case 0xff:
        return MidiReader::STATUS_META;
    }

    return MidiReader::STATUS_NONE;
  }

  /**
   * @param \PhpTabs\Reader\Midi\MidiSequence $sequence
   */
  private function initFields(MidiSequence $sequence)
  {
    $this->resolution = $sequence->getResolution();
    $this->channels = array();
    $this->headers = array();
    $this->tracks = array();
    $this->tempNotes = array();
    $this->tempChannels = array();
    $this->trackTuningHelpers = array();
    $this->channelRouter = new ChannelRouter();
  }

  /**
   * @param mixed $tick
   * @param int $track
   * @param int $channel
   * @param byte $value
   */
  private function makeNote($tick, $track, $channel, $value)
  {
    $tempNote = $this->getTempNote($track, $channel, $value, true);

    if ($tempNote !== null) {
      $nString = 0;
      $nValue = $tempNote->getValue() + $this->settings->getTranspose();
      $nVelocity = $tempNote->getVelocity();
      $nStart = $tempNote->getTick();

      $minDuration = new Duration();
      $minDuration->setValue(Duration::SIXTY_FOURTH);
      $nDuration = Duration::fromTime($tick - $tempNote->getTick(), $minDuration);

      $measure = $this->getMeasure($this->getTrack($track), $tempNote->getTick());
      $beat = $measure->getBeatByStart($nStart);
      $beat->getVoice(0)->getDuration()->copyFrom($nDuration);

      $note = new Note();
      $note->setValue($nValue);
      $note->setString($nString);
      $note->setVelocity($nVelocity);

      // Effect Bends / Vibrato
      if ($tempNote->countPitchBends() > 0) {
        $this->makeNoteEffect($note, $tempNote->getPitchBends());
      }

      $beat->getVoice(0)->addNote($note);
    }
  }

  /**
   * @param \PhpTabs\Music\Note $note
   * @param array $pitchBends
   */
  private function makeNoteEffect(Note $note, array $pitchBends)
  {
    $tmp = array();

    foreach ($pitchBends as $pitchBend) {
      if (!in_array($pitchBend, $tmp)) {
        array_push($tmp, $pitchBend);
      }
    }

    // All pitches have the same value: vibrato
    if (count($tmp) == 1) {
      $note->getEffect()->setVibrato(true);

      return;
    }

    // Bend
    $bend = new EffectBend();
    $bend->addPoint(0, 0);

    foreach ($pitchBends as $k => $pitchBend) {
      $bend->addPoint($k, $pitchBend);
    }

    $note->getEffect()->setBend($bend);
  }
  
  /**
   * @param mixed $tick
   * @param int $track
   */
  private function makeTempNotesBefore($tick, $track)
  {
    $nextTick = $tick;

    $countTempNotes = count($this->tempNotes);

    for ($i = 0; $i < $countTempNotes; $i++) {
      $note = $this->tempNotes[$i];

      if ($note->getTick() < $nextTick && $note->getTrack() == $track) {
        $nextTick = $note->getTick() + (Duration::QUARTER_TIME * 5); //First beat + 4/4 measure;
        $this->makeNote($nextTick, $track, $note->getChannel(), $note->getValue());
        break;
      }
    }
  }

  /**
   * @param array $data
   */
  private function parseControlChange(array $data)
  {
    $length = count($data);
    $channel = ($length > 0)?(($data[0] & 0xff) & 0x0f):-1;
    $control = ($length > 1)?($data[1] & 0xff):-1;
    $value = ($length > 2)?($data[2] & 0xff):-1;

    if ($channel != -1 && $control != -1 && $value != -1) {
      if ($control == MidiSettings::VOLUME) {
        $this->getTempChannel($channel)->setVolume($value);
      } elseif ($control == MidiSettings::BALANCE) {
        $this->getTempChannel($channel)->setBalance($value);
      }
    }
  }

  /**
   * @param int $track
   * @param mixed $tick
   * @param array $data
   */
  private function parseNoteOff($track, $tick, array $data)
  {
    $length = count($data);

    $channel = $length > 0 ? (($data[0] & 0xff) & 0x0f) : 0;
    $value = $length > 1 ? ($data[1] & 0xff) : 0;

    $this->makeNote($tick, $track, $channel, $value);
  }

  /**
   * @param int $track
   * @param mixed $tick
   * @param array $data
   */
  private function parseNoteOn($track, $tick, array $data)
  {
    $length = count($data);
    $channel = $length > 0 ? (($data[0] & 0xff) & 0x0f) : 0;
    $value = $length > 1 ? ($data[1] & 0xff) : 0;
    $velocity = $length > 2 ? ($data[2] & 0xff) : 0;

    if ($velocity == 0) {
      $this->parseNoteOff($track, $tick, $data);
    } elseif ($value > 0) {
      $this->makeTempNotesBefore($tick, $track);
      $this->getTempChannel($channel)->setTrack($track);
      $this->getTrackTuningHelper($track)->checkValue($value);

      $this->tempNotes[] = new MidiNote($track, $channel, $tick, $value, $velocity);
    }
  }

  /**
   * @param array $data
   */
  private function parsePitchBend(array $data)
  {
    $length = count($data);

    // Resolution
    $value = $length > 2 ? ($data[2] - 0x40) : 0;

    if ($value !== 0 && count($this->tempNotes) > 0) {
      $noteCount = count($this->tempNotes);

      $this->tempNotes[$noteCount-1]->addPitchBend($value);
    }
  }

  /**
   * @param array $data
   */
  private function parseProgramChange(array $data)
  {
    $length = count($data);
    $channel = $length > 0 ? (($data[0] & 0xff) & 0x0f) : -1;
    $instrument = $length > 1 ? ($data[1] & 0xff):-1;

    if ($channel != -1 && $instrument != -1) {
      $this->getTempChannel($channel)->setInstrument($instrument);
    }
  }

  /**
   * @param mixed $tick
   * @param array $data
   */
  private function parseTempo($tick, array $data)
  {
    if (count($data) >= 3) {
      $tempo = Tempo::fromTPQ(($data[2] & 0xff) | (($data[1] & 0xff) << 8) | (($data[0] & 0xff) << 16));

      $this->getHeader($tick)->setTempo($tempo);
    }
  }

  /**
   * @param mixed $tick
   */
  private function parseTick($tick)
  {
    return abs(Duration::QUARTER_TIME * $tick / $this->resolution);
  }

  /**
   * @param mixed $tick
   * @param array $data
   */
  private function parseTimeSignature($tick, array $data)
  {
    if (count($data) >= 2) {
      $timeSignature = new TimeSignature();
      $timeSignature->setNumerator($data[0]);
      $timeSignature->getDenominator()->setValue(Duration::QUARTER);

      switch ($data[1]) {
        case 0:
          $timeSignature->getDenominator()->setValue(Duration::WHOLE);
          break;
        case 1:
          $timeSignature->getDenominator()->setValue(Duration::HALF);
          break;
        case 2:
          $timeSignature->getDenominator()->setValue(Duration::QUARTER);
          break;
        case 3:
          $timeSignature->getDenominator()->setValue(Duration::EIGHTH);
          break;
        case 4:
          $timeSignature->getDenominator()->setValue(Duration::SIXTEENTH);
          break;
        case 5:
          $timeSignature->getDenominator()->setValue(Duration::THIRTY_SECOND);
          break;
      }

      $this->getHeader($tick)->setTimeSignature($timeSignature);
    }
  }

  /**
   * @param \PhpTabs\Reader\Midi\MidiTrackReaderHelper $helper
   */
  private function readEvent(MidiTrackReaderHelper $helper)
  {
    $statusByte = $this->readUnsignedByte();
    $helper->remainingBytes--;
    $savedByte = 0;
    $runningStatusApplies = false;

    if ($statusByte < 0x80) {
      switch ($helper->runningStatusByte) {
        case -1:
          throw new Exception('Corrupted MIDI file: status byte is missing');
          break;
        default:
          $runningStatusApplies = true;
          $savedByte = $statusByte;
          $statusByte = $helper->runningStatusByte;
          break;
      }
    }

    $type = $this->getType($statusByte);

    if ($type == MidiReader::STATUS_ONE_BYTE) {
      $data = 0;

      if ($runningStatusApplies) {
        $data = $savedByte;
      } else {
        $data = $this->readUnsignedByte();
        $helper->remainingBytes--;
        $helper->runningStatusByte = $statusByte;
      }

      return new MidiEvent(MidiMessage::shortMessage(($statusByte & 0xf0), ($statusByte & 0x0f) , $data), $helper->ticks);

    } elseif ($type == MidiReader::STATUS_TWO_BYTES) {
      $data1 = 0;

      if ($runningStatusApplies) {
        $data1 = $savedByte;
      } else {
        $data1 = $this->readUnsignedByte();
        $helper->remainingBytes--;
        $helper->runningStatusByte = $statusByte;
      }

      $helper->remainingBytes--;

      return new MidiEvent(MidiMessage::shortMessage(($statusByte & 0xf0), ($statusByte & 0x0f), $data1, $this->readUnsignedByte()), $helper->ticks);

    } elseif ($type == MidiReader::STATUS_SYSEX) {
      if (MidiReader::CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX) {
        $helper->runningStatusByte = -1;
      }

      $dataLength = $this->readVariableLengthQuantity($helper);
      $data = array();

      for ($i = 0; $i < $dataLength; $i++) {
        $data[$i] = $this->readUnsignedByte();
        $helper->remainingBytes--;
      }

    } elseif ($type == MidiReader::STATUS_META) {
      if (MidiReader::CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX) {
        $helper->runningStatusByte = -1;
      }

      $typeByte = $this->readUnsignedByte();
      $helper->remainingBytes--;
      $dataLength = $this->readVariableLengthQuantity($helper);
      $data = array();

      for ($i = 0; $i < $dataLength; $i++) {
        $data[$i] = $this->readUnsignedByte();
        $helper->remainingBytes--;
      }

      return new MidiEvent(MidiMessage::metaMessage($typeByte, $data), $helper->ticks);
    }

    return null;
  }

  /**
   * @param \PhpTabs\Reader\Midi\MidiTrack $track
   */
  private function readTrack(MidiTrack $track)
  {
    while (true) {
      if ($this->readInt() == MidiReaderInterface::TRACK_MAGIC) {
        break;
      }

      $chunkLength = $this->readInt();

      if ($chunkLength % 2 != 0) {
        $chunkLength++;
      }

      $this->skip($chunkLength);
    }

    $helper = new MidiTrackReaderHelper(0, $this->readInt(), -1);

    while ($helper->remainingBytes > 0) {
      $helper->ticks += $this->readVariableLengthQuantity($helper);

      $event = $this->readEvent($helper);

      if ($event !== null) {
        $track->add($event);
      }
    }
  }
}
