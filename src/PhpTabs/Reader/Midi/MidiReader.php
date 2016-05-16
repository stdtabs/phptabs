<?php

namespace PhpTabs\Reader\Midi;

use PhpTabs\Component\Config;
use PhpTabs\Component\File;
use PhpTabs\Component\Tablature;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Channel;
use PhpTabs\Model\Color;
use PhpTabs\Model\Duration;
use PhpTabs\Model\Helper;
use PhpTabs\Model\Measure;
use PhpTabs\Model\MeasureHeader;
use PhpTabs\Model\Note;
use PhpTabs\Model\Song;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\TimeSignature;
use PhpTabs\Model\Track;

/**
 * MIDI Reader
 */
class MidiReader extends MidiReaderBase
{
  const CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX = true;
  const STATUS_NONE = 0;
  const STATUS_ONE_BYTE = 1;
  const STATUS_TWO_BYTES = 2;
  const STATUS_SYSEX = 3;
  const STATUS_META = 4;

  /** @var array $supportedVersions */
  private static $supportedVersions = array("MThd\0\0\0\6");

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
   * Constructor
   * @param File $file input file to read
   * @return void
   */
  public function __construct(File $file)
  {
    parent::__construct($file);

    $this->song = new Song();

    $this->setTablature($this->song);

    $this->settings = MidiSettings::getDefaults();
    $this->sequence = $this->getSequence();
    $this->initFields($this->sequence);

    $countTracks = $this->sequence->countTracks();

    for($i = 0; $i < $countTracks; $i++)
    {
      $track = $this->sequence->getTrack($i);
      $trackNumber = $this->getNextTrackNumber();
      $events = $track->countEvents();
      for($j = 0; $j < $events; $j++)
      {
        $event = $track->get($j);
        $this->parseMessage($trackNumber, $event->getTick(), $event->getMessage());
      }
    }

    $this->checkAll();

    foreach($this->channels as $channel)
    {
      $this->song->addChannel($channel);
    }

    foreach($this->headers as $header)
    {
      $this->song->addMeasureHeader($header);
    }

    foreach($this->tracks as $track)
    {
      $this->song->addTrack($track);
    }

    $this->closeStream();
  }

  /**
   * @return array of supported versions
   */
  public function getSupportedVersions()
  {
    return self::$supportedVersions;
  }

  /**
   * {@inheritdoc}
   */
  public function getTablature()
  {
    if(isset($this->tablature))
    {
      return $this->tablature;
    }

    return new Tablature();
  }

  /**
   * Initializes Tablature with read Song
   * 
   * @param Song $song as read from file
   */
  private function setTablature(Song $song)
  {
    if(!isset($this->tablature))
    {
      $this->tablature = new Tablature();
    }

    $this->tablature->setSong($song);
  }

  /*-------------------------------------------------------------------
   * Private methods are below
   * -----------------------------------------------------------------*/
  private function checkAll()
  {
    $this->checkChannels();
    $this->checkTracks();

    $headerCount = count($this->headers);
    for($i = 0; $i < count($this->tracks); $i++)
    {
      $track = $this->tracks[$i];

      while($track->countMeasures() < $headerCount)
      {
        $start = Duration::QUARTER_TIME;
        $lastMeasure = $track->countMeasures() > 0
          ? $track->getMeasure($track->countMeasures() - 1) : null;
        if($lastMeasure != null)
        {
          $start = ($lastMeasure->getStart() + $lastMeasure->getLength());
        }

        $track->addMeasure(new Measure($this->getHeader($start)));
      }
    }

    if(!count($this->headers) || !count($this->tracks))
    {
      throw new \Exception("Empty Song");
    }
  }

  private function checkChannels()
  {
    for($tc = 0; $tc < count($this->tempChannels); $tc++)
    {
      $tempChannel = $this->tempChannels[$tc];
      if($tempChannel->getTrack() > 0)
      {
        $channelExists = false;
        for($c = 0; $c < count($this->channels); $c++)
        {
          $channel = $this->channels[$c];
          $channelRoute = $this->channelRouter->getRoute($channel->getChannelId());
          if($channelRoute !== null)
          {
            if($channelRoute->getChannel1() == $tempChannel->getChannel() 
              || $channelRoute->getChannel2() == $tempChannel->getChannel())
            {
              $channelExists = true;
            }
          }
        }

        if(!$channelExists)
        {
          $channel = new Channel();
          $channel->setChannelId(count($this->channels) + 1);
          $channel->setProgram($tempChannel->getInstrument());
          $channel->setVolume($tempChannel->getVolume());
          $channel->setBalance($tempChannel->getBalance());
          $channel->setName(("#" + $channel->getChannelId()));
          $channel->setBank($tempChannel->getChannel() == 9
            ? Channel::DEFAULT_PERCUSSION_BANK : Channel::DEFAULT_BANK);

          $channelRoute = new MidiChannelRoute($channel->getChannelId());
          $channelRoute->setChannel1($tempChannel->getChannel());
          $channelRoute->setChannel2($tempChannel->getChannel());

          for($tcAux = ($tc + 1); $tcAux < count($this->tempChannels); $tcAux++)
          {
            $tempChannelAux = $this->tempChannels[$tcAux];
            if($tempChannel->getTrack() == $tempChannelAux->getTrack())
            {
              if($channelRoute->getChannel2() == $channelRoute->getChannel1())
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
    foreach($this->tracks as $track)
    {
      $trackChannel = null;

      foreach($this->tempChannels as $tempChannel)
      {
        if($tempChannel->getTrack() == $track->getNumber())
        {
          foreach($this->channels as $channel)
          {
            $channelRoute = $this->channelRouter->getRoute($channel->getChannelId());
            if($channelRoute !== null && $tempChannel->getChannel() == $channelRoute->getChannel1())
            {
              $trackChannel = $channel;
            }
          }
        }
      }
      if($trackChannel !== null)
      {
        $track->setChannelId($trackChannel->getChannelId());
      }
      if($trackChannel !== null && $trackChannel->isPercussionChannel())
      {
        $track->setStrings(Helper::createPercussionStrings(6)); 
      }
      else
      {
        $track->setStrings($this->getTrackTuningHelper($track->getNumber())->getStrings());
      }
    }
  }

  private function getBeat(Measure $measure, $start)
  {
    $beatCount = $measure->countBeats();
    for($i = 0; $i < $beatCount; $i++)
    {
      $beat = $measure->getBeat($i);
      if($beat->getStart() == $start)
      {
        return $beat;
      }
    }

    $beat = new Beat();
    $beat->setStart($start);
    $measure->addBeat($beat);
    return $beat;
  }

  private function getHeader($tick)
  {
    $realTick = $tick >= Duration::QUARTER_TIME
      ? $tick : Duration::QUARTER_TIME;

    foreach($this->headers as $header)
    {
      if($realTick >= $header->getStart() && $realTick < $header->getStart() + $header->getLength())
      {
        return $header;
      }
    }
    $last = $this->getLastHeader();
    $header = new MeasureHeader();
    $header->setNumber($last !== null ? $last->getNumber() + 1 : 1);
    $header->setStart($last !== null ? ($last->getStart() + $last->getLength()) : Duration::QUARTER_TIME);
    $header->getTempo()->setValue($last !== null ? $last->getTempo()->getValue() : 120);
    if($last !== null)
    {
      $header->getTimeSignature()->copyFrom($last->getTimeSignature());
    }
    else
    {
      $header->getTimeSignature()->setNumerator(4);
      $header->getTimeSignature()->getDenominator()->setValue(Duration::QUARTER);
    }
    $this->headers[] = $header;

    if($realTick >= $header->getStart() && $realTick < $header->getStart() + $header->getLength())
    {
      return $header;
    }
    return $this->getHeader($realTick);
  }

  private function getLastHeader()
  {
    if(count($this->headers))
    {
      return $this->headers[count($this->headers) - 1];
    }
    return null;
  }

  private function getMeasure(Track $track, $tick)
  {
    $realTick = ($tick >= Duration::QUARTER_TIME)
      ? $tick : Duration::QUARTER_TIME;

    $measures = $track->getMeasures();

    foreach($measures as $measure)
    {
      if($realTick >= $measure->getStart() && $realTick < $measure->getStart() + $measure->getLength())
      {
        return $measure;
      }
    }

    $this->getHeader($realTick);
    for($i = 0; $i < count($this->headers); $i++)
    {
      $exist = false;
      $header = $this->headers[$i];
      $measureCount = $track->countMeasures();
      for($j = 0; $j < $measureCount; $j++)
      {
        $measure = $track->getMeasure($j);
        if($measure->getHeader() == $header)
        {
          $exist = true;
        }
      }

      if(!$exist)
      {
        $measure = new Measure($header);
        $track->addMeasure($measure);
      }
    }
    return $this->getMeasure($track, $realTick);
  }

  private function getNextTrackNumber()
  {
    return (count($this->tracks) + 1);
  }

  public function getTempChannel($channel)
  {
    foreach($this->tempChannels as $tempChannel)
    {
      if($tempChannel->getChannel() == $channel)
      {
        return $tempChannel;
      }
    }
    $tempChannel = new MidiChannel($channel);
    $this->tempChannels[] = $tempChannel;

    return $tempChannel;
  }

  private function getTempNote($track, $channel, $value, $purge)
  {
    $countTempNotes = count($this->tempNotes);

    for($i = 0; $i < $countTempNotes; $i++)
    {
      $note = $this->tempNotes[$i];
      if($note->getTrack() == $track && $note->getChannel() == $channel && $note->getValue() == $value)
      {
        if($purge)
        {
          array_splice($this->tempNotes, $i, 1);
        }
        return $note;
      }
    }
    return null;
  }

  private function getTrackTuningHelper($track)
  {
    foreach($this->trackTuningHelpers as $helper)
    {
      if($helper->getTrack() == $track)
      {
        return $helper;
      }
    }
    $helper = new MidiTrackTuningHelper($track);
    $this->trackTuningHelpers[] = $helper;

    return $helper;
  }

  private function parseMessage($trackNumber, $tick, MidiMessage $message)
  {
    $parsedTick = $this->parseTick($tick + $this->resolution);

    //NOTE ON
    if($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::NOTE_ON)
    {
      $this->parseNoteOn($trackNumber, $parsedTick, $message->getData());
    }
    //NOTE OFF
    else if($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::NOTE_OFF)
    {
      $this->parseNoteOff($trackNumber, $parsedTick,$message->getData());
    }
    //PROGRAM CHANGE
    else if($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::PROGRAM_CHANGE)
    {
      $this->parseProgramChange($message->getData());
    }
    //CONTROL CHANGE
    else if($message->getType() == MidiMessage::TYPE_SHORT && $message->getCommand() == MidiMessage::CONTROL_CHANGE)
    {
      $this->parseControlChange($message->getData());
    }
    //TIME SIGNATURE
    else if($message->getType() == MidiMessage::TYPE_META && $message->getCommand() == MidiMessage::TIME_SIGNATURE_CHANGE)
    {
      $this->parseTimeSignature($parsedTick, $message->getData());
    }
    //TEMPO
    else if($message->getType() == MidiMessage::TYPE_META && $message->getCommand() == MidiMessage::TEMPO_CHANGE)
    {
      $this->parseTempo($parsedTick, $message->getData());
    }
  }

  private function getSequence()
  {
    if ($this->readInt() != MidiReaderInterface::HEADER_MAGIC)
    {
      throw new \Exception("not a MIDI file: wrong header magic");
    }
    $headerLength = $this->readInt();
    if ($headerLength < MidiReaderInterface::HEADER_LENGTH)
    {
      throw new \Exception("corrupt MIDI file: wrong header length");
    }
    $type = $this->readShort();
    if ($type < 0 || $type > 2)
    {
      throw new \Exception("corrupt MIDI file: illegal type");
    }
    if ($type == 2)
    {
      throw new \Exception("this implementation doesn't support type 2 MIDI files");
    }
    $trackCount = $this->readShort();
    if ($trackCount <= 0)
    {
      throw new \Exception("corrupt MIDI file: number of tracks must be positive");
    }
    if ($type == 0 && $trackCount != 1)
    {
      throw new \Exception("corrupt MIDI file:  type 0 files must contain exactly one track $trackCount");
    }
    $divisionType = -1.0;
    $resolution = -1;
    $division = $this->readUnsignedShort();
    if (($division & 0x8000) != 0)
    {
      $frameType = -(($division >> 8) & 0xFF);
      switch($frameType)
      {
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
          throw new \Exception("corrupt MIDI file: illegal frame division type");
          break;
      }

      $resolution = $division & 0xff;
    }
    else
    {
      $divisionType = MidiReaderInterface::PPQ;
      $resolution = $division & 0x7fff;
    }
    $this->skip($headerLength - MidiReaderInterface::HEADER_LENGTH);

    $sequence = new MidiSequence($divisionType, $resolution);

    for ($i = 0; $i < $trackCount; $i++)
    {
      $track = new MidiTrack();
      $sequence->addTrack($track);
      $this->readTrack($track);
    }

    return $sequence;
  }

  private function getTrack($number)
  {
    foreach($this->tracks as $track)
    {
      if($track->getNumber() == $number)
      {
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

  private function getType($statusByte)
  {
    if ($statusByte < 0xf0)
    {
      $command = $statusByte & 0xf0;
      if($command == 0x80 || $command == 0x90 || $command == 0xa0 || $command == 0xb0 || $command == 0xe0)
      {
        return MidiReader::STATUS_TWO_BYTES;
      }
      else if($command == 0xc0 || $command == 0xd0)
      {
        return MidiReader::STATUS_ONE_BYTE;
      }

      return MidiReader::STATUS_NONE;
    }
    else if ($statusByte == 0xf0 || $statusByte == 0xf7)
    {
      return MidiReader::STATUS_SYSEX;
    }
    else if ($statusByte == 0xff)
    {
      return MidiReader::STATUS_META;
    }
    else
    {
      return MidiReader::STATUS_NONE;
    }
  }

  private function initFields(MidiSequence $sequence)
  {
    $this->resolution = $sequence->getResolution();
    $this->channels = array();
    $this->headers = array();
    $this->tracks = array();
    $this->tempNotes = array();
    $this->tempChannels = array();
    $this->trackTuningHelpers = array();
    $this->channelRouter = new MidiChannelRouter();
  }

  private function makeNote($tick, $track, $channel, $value)
  {
    $tempNote = $this->getTempNote($track, $channel, $value, true);
    if($tempNote != null)
    {
      $nString = 0;
      $nValue = ($tempNote->getValue() + $this->settings->getTranspose());
      $nVelocity = 64;
      $nStart = $tempNote->getTick();
      $minDuration = new Duration();
      $minDuration->setValue(Duration::SIXTY_FOURTH);
      $nDuration = Duration::fromTime($tick - $tempNote->getTick(), $minDuration);

      $measure = $this->getMeasure($this->getTrack($track), $tempNote->getTick());
      $beat = $this->getBeat($measure, $nStart);
      $beat->getVoice(0)->getDuration()->copyFrom($nDuration);

      $note = new Note();
      $note->setValue($nValue);
      $note->setString($nString);
      $note->setVelocity($nVelocity);

      $beat->getVoice(0)->addNote($note);
    }
  }

  private function makeTempNotesBefore($tick, $track)
  {
    $nextTick = $tick;

    $countTempNotes = count($this->tempNotes);
    for($i = 0; $i < $countTempNotes; $i++)
    {
      $note = $this->tempNotes[$i];
      if($note->getTick() < $nextTick && $note->getTrack() == $track)
      {
        $nextTick = $note->getTick() + (Duration::QUARTER_TIME * 5); //First beat + 4/4 measure;
        $this->makeNote($nextTick, $track, $note->getChannel(), $note->getValue());
        break;
      }
    }
  }

  private function parseControlChange(array $data)
  {
    $length = count($data);
    $channel = ($length > 0)?(($data[0] & 0xFF) & 0x0F):-1;
    $control = ($length > 1)?($data[1] & 0xFF):-1;
    $value = ($length > 2)?($data[2] & 0xFF):-1;
    if($channel != -1 && $control != -1 && $value != -1)
    {
      if($control == MidiSettings::VOLUME)
      {
        $this->getTempChannel($channel)->setVolume($value);
      }
      else if($control == MidiSettings::BALANCE)
      {
        $this->getTempChannel($channel)->setBalance($value);
      }
    }
  }

  private function parseNoteOff($track, $tick, array $data)
  {
    $length = count($data);

    $channel = $length > 0 ? (($data[0] & 0xFF) & 0x0F) : 0;
    $value = $length > 1 ? ($data[1] & 0xFF) : 0;

    $this->makeNote($tick, $track, $channel, $value);
  }

  private function parseNoteOn($track, $tick, array $data)
  {
    $length = count($data);
    $channel = $length > 0 ? (($data[0] & 0xFF) & 0x0F) : 0;
    $value = $length > 1 ? ($data[1] & 0xFF) : 0;
    $velocity = $length > 2 ? ($data[2] & 0xFF) : 0;
    if($velocity == 0)
    {
      $this->parseNoteOff($track, $tick, $data);
    }
    else if($value > 0)
    {
      $this->makeTempNotesBefore($tick, $track);
      $this->getTempChannel($channel)->setTrack($track);
      $this->getTrackTuningHelper($track)->checkValue($value);
      $this->tempNotes[] = new MidiNote($track, $channel, $value, $tick);
    }
  }

  private function parseProgramChange(array $data)
  {
    $length = count($data);
    $channel = $length > 0 ? (($data[0] & 0xFF) & 0x0F) : -1;
    $instrument = $length > 1 ? ($data[1] & 0xFF):-1;
    if($channel != -1 && $instrument != -1)
    {
      $this->getTempChannel($channel)->setInstrument($instrument);
    }
  }

  private function parseTempo($tick, array $data)
  {
    if(count($data) >= 3)
    {
      $tempo = Tempo::fromTPQ(($data[2] & 0xff) | (($data[1] & 0xff) << 8) | (($data[0] & 0xff) << 16));
      $this->getHeader($tick)->setTempo($tempo);
    }
  }

  private function parseTick($tick)
  {
    return abs(Duration::QUARTER_TIME * $tick / $this->resolution);
  }

  private function parseTimeSignature($tick, array $data)
  {
    if(count($data) >= 2)
    {
      $timeSignature = new TimeSignature();
      $timeSignature->setNumerator($data[0]);
      $timeSignature->getDenominator()->setValue(Duration::QUARTER);

      if ($data[1] == 0)
      {
        $timeSignature->getDenominator()->setValue(Duration::WHOLE);
      }
      else if ($data[1] == 1)
      {
        $timeSignature->getDenominator()->setValue(Duration::HALF);
      }
      else if ($data[1] == 2)
      {
        $timeSignature->getDenominator()->setValue(Duration::QUARTER);
      }
      else if ($data[1] == 3)
      {
        $timeSignature->getDenominator()->setValue(Duration::EIGHTH);
      }
      else if ($data[1] == 4)
      {
        $timeSignature->getDenominator()->setValue(Duration::SIXTEENTH);
      }
      else if ($data[1] == 5)
      {
        $timeSignature->getDenominator()->setValue(Duration::THIRTY_SECOND);
      }

      $this->getHeader($tick)->setTimeSignature($timeSignature);
    }
  }

  private function readEvent(MidiTrackReaderHelper $helper)
  {
    $statusByte = $this->readUnsignedByte();
    $helper->remainingBytes--;
    $savedByte = 0;
    $runningStatusApplies = false;

    if ($statusByte < 0x80)
    {
      switch($helper->runningStatusByte)
      {
        case -1:
          throw new \Exception("corrupt MIDI file: status byte missing");
          break;
        default:
          $runningStatusApplies = true;
          $savedByte = $statusByte;
          $statusByte = $helper->runningStatusByte;
          break;
      }
    }

    $type = $this->getType($statusByte);
    if($type == MidiReader::STATUS_ONE_BYTE)
    {
      $data = 0;
      if ($runningStatusApplies)
      {
        $data = $savedByte;
      }
      else
      {
        $data = $this->readUnsignedByte();
        $helper->remainingBytes--;
        $helper->runningStatusByte = $statusByte;
      }

      return new MidiEvent(MidiMessage::shortMessage(($statusByte & 0xF0), ($statusByte & 0x0F) , $data), $helper->ticks);
    }
    else if($type == MidiReader::STATUS_TWO_BYTES)
    {
      $data1 = 0;
      if ($runningStatusApplies)
      {
        $data1 = $savedByte;
      }
      else
      {
        $data1 = $this->readUnsignedByte();
        $helper->remainingBytes--;
        $helper->runningStatusByte = $statusByte;
      }

      $helper->remainingBytes--;
      return new MidiEvent(MidiMessage::shortMessage(($statusByte & 0xF0), ($statusByte & 0x0F), $data1, $this->readUnsignedByte()), $helper->ticks);
    }
    else if($type == MidiReader::STATUS_SYSEX)
    {
      if (MidiReader::CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX)
      {
        $helper->runningStatusByte = -1;
      }
      $dataLength = $this->readVariableLengthQuantity($helper);
      $data = array();
      for ($i = 0; $i < $dataLength; $i++)
      {
        $data[$i] = $this->readUnsignedByte();
        $helper->remainingBytes--;
      }
    }
    else if($type == MidiReader::STATUS_META)
    {
      if (MidiReader::CANCEL_RUNNING_STATUS_ON_META_AND_SYSEX)
      {
        $helper->runningStatusByte = -1;
      }
      $typeByte = $this->readUnsignedByte();
      $helper->remainingBytes--;
      $dataLength = $this->readVariableLengthQuantity($helper);
      $data = array();
      for ($i = 0; $i < $dataLength; $i++)
      {
        $data[$i] = $this->readUnsignedByte();
        $helper->remainingBytes--;
      }

      return new MidiEvent(MidiMessage::metaMessage($typeByte, $data), $helper->ticks);
    }

    return null;
  }


  private function readTrack(MidiTrack $track)
  {
    while (true)
    {
      if ($this->readInt() == MidiReaderInterface::TRACK_MAGIC)
      {
        break;
      }
      $chunkLength = $this->readInt();
      if ($chunkLength % 2 != 0)
      {
        $chunkLength++;
      }
      $this->skip($chunkLength);
    }

    $helper = new MidiTrackReaderHelper(0, $this->readInt(), -1);
    while ($helper->remainingBytes > 0)
    {
      $helper->ticks += $this->readVariableLengthQuantity($helper);
      $event = $this->readEvent($helper);
      if($event != null)
      {
        $track->add($event);
      }
    }
  }
}
