<?php

namespace PhpTabs\Writer\Midi;

use Exception;
use PhpTabs\Model\Song;
use PhpTabs\Model\ChannelRouter;
use PhpTabs\Model\ChannelRouterConfigurator;
use PhpTabs\Reader\Midi\MidiReaderInterface;
use PhpTabs\Reader\Midi\MidiEvent;
use PhpTabs\Reader\Midi\MidiMessage;
use PhpTabs\Reader\Midi\MidiReader;
use PhpTabs\Reader\Midi\MidiTrack;
use PhpTabs\Reader\Midi\MidiSequence;
use PhpTabs\Reader\Midi\MidiSettings;

class MidiWriter extends MidiWriterBase
{  
  const ADD_DEFAULT_CONTROLS = 0x01;
  const ADD_MIXER_MESSAGES = 0x02;
  const ADD_METRONOME = 0x04;
  const ADD_FIRST_TICK_MOVE = 0x08;
  const BANK_SELECT = 0x00;
  const VOLUME = 0x07;
  const BALANCE = 0x0A;
  const EXPRESSION = 0x0B;
  const REVERB = 0x5B;
  const TREMOLO = 0x5C;
  const CHORUS = 0x5D;
  const PHASER = 0x5F;
  const DATA_ENTRY_MSB = 0x06;
  const DATA_ENTRY_LSB = 0x26;
  const RPN_LSB = 0x64 ;
  const RPN_MSB = 0x65 ;
  const ALL_NOTES_OFF = 0x7B;

  public function __construct(Song $song)
  {
    parent::__construct();

    # Build sequence
    $channelRouter = new ChannelRouter();
    $channelRouterConfigurator = new ChannelRouterConfigurator($channelRouter);
    $channelRouterConfigurator->configureRouter($song->getChannels());
    $settings = (new MidiSettings())->getdefaults();

    $midiSequenceParser = new MidiSequenceParser($song, 
      (self::ADD_FIRST_TICK_MOVE | self::ADD_DEFAULT_CONTROLS | self::ADD_MIXER_MESSAGES)
    );
    $midiSequenceParser->setTranspose($settings->getTranspose());
    $midiSequenceParser->parse(new MidiSequenceHandler($song->countTracks() + 1, $channelRouter, $this));
  }

  public function write(MidiSequence $sequence, $type)
  {
    $this->writeInt(MidiReaderInterface::HEADER_MAGIC);
    $this->writeInt(MidiReaderInterface::HEADER_LENGTH);
    $this->writeShort($type);

    # Write sequences
    $this->writeShort($sequence->countTracks());
    $this->writeShort($sequence->getDivisionType() == MidiSequence::PPQ
      ? ($sequence->getResolution() & 0x7fff) : 0
    );

    for ($i = 0; $i < $sequence->countTracks(); $i++)
    {
      $this->writeTrack($sequence->getTrack($i));
    }
  }

  private function writeTrack(MidiTrack $track)
  {
    $length = 0;
    $this->writeInt(MidiReader::TRACK_MAGIC);
    $previous = null;

    for ($i = 0; $i < $track->countEvents(); $i++)
    {
      $event = $track->get($i);
      
      $length += $this->writeEvent($event, $previous);
      $previous = $event;
    }

    return $length;
  }

  private function writeEvent(MidiEvent $event, MidiEvent $previous = null)
  {
    $length = $this->writeVariableLengthQuantity($previous !== null
      ? ($event->getTick() - $previous->getTick()) : 0);

    $message = $event->getMessage();

    if($message->getType() == MidiMessage::TYPE_SHORT)
    {
      $length += $this->writeShortMessage($message);
    }
    else if($message->getType() == MidiMessage::TYPE_META)
    {
      $length += $this->writeMetaMessage($message);
    }

    return $length;
  }

  private function writeShortMessage(MidiMessage $message)
  {
    $data = $message->getData();

    $length = count($data);
    $this->writeUnsignedBytes($message->getData());

    return $length;
  }

  protected function writeMetaMessage(MidiMessage $message)
  {
    $length = 0;
    $data = $message->getData();

    $this->writeUnsignedBytes(array(255));
    $this->writeUnsignedBytes(array($message->getCommand()));
    $length += 2;

    if(is_array($data))
    {
      $length += $this->writeVariableLengthQuantity(count($data));
      $this->writeUnsignedBytes($data);
      $length += count($data);
    }
    else
    {
      $length += $this->writeVariableLengthQuantity(strlen($data));
      $this->writeUnsignedBytes(array($data));
      $length += strlen($data);
    }

    return $length;
  }
}
