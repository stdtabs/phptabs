<?php

namespace PhpTabs\Reader\Midi;

use PhpTabs\Component\File;
use PhpTabs\Component\Log;
use PhpTabs\Model\Song;

/**
 * MIDI methods for Readers
 */
abstract class MidiReaderBase implements MidiReaderInterface
{
  /** @var int */
  private $versionIndex;

  /** @var string */
  private $version;

  /** @var File */
  private $file;

  /**
   * Constructor
   * 
   * @param File $file input file to read
   * @return void
   */
  public function __construct(File $file)
  {
    $this->file = $file;
  }

  /**
   * Reads a 32 bit integer big endian
   * 
   * @return integer
   */
  protected function readInt()
  {
    $bytes = array();

    for($i=0; $i<=3; $i++)
    {
      $bytes[$i] = ord($this->file->getStream());
    }

    return ($bytes[3] & 0xff) | (($bytes[2] & 0xff) << 8) 
      | (($bytes[1] & 0xff) << 16) | (($bytes[0] & 0xff) << 24);
  }

  /**
   * Reads a 16 bit integer big endian
   * 
   * @return integer
   */
  protected function readShort()
  {
    $bytes = array();

    for($i=0; $i<=1; $i++)
    {
      $bytes[$i] = ord($this->file->getStream());
    }

    return (($bytes[0] & 0xff) << 8) | ($bytes[1] & 0xff);
  }

  /**
   * Reads an unsigned 16 bit integer big endian
   * 
   * @return integer
   */
  protected function readUnsignedShort()
  {
    $bytes = array();

    for($i=0; $i<=1; $i++)
    {
      $bytes[$i] = ord($this->file->getStream());
    }

    return (($bytes[0] & 0x7f) << 8) | ($bytes[1] & 0xff);
  }

  /**
   * @param MidiTrackReaderHelper $helper
   * @return integer
   */
  public function readVariableLengthQuantity(MidiTrackReaderHelper $helper)
  {
    $count = 0;
    $value = 0;
    while ($count < 4)
    {
      $data = $this->readUnsignedByte();
      $helper->remainingBytes--;
      $count++;
      $value <<= 7;
      $value |= ($data & 0x7f);
      if ($data < 128)
      {
        return $value;
      }
    }
    throw new \Exception("not a MIDI file: unterminated variable-length quantity");
  }

  /**
   * Reads an unsigned byte
   * 
   * @return byte
   */
  protected function readUnsignedByte()
  {
    return unpack('C', $this->file->getStream())[1];
  }

  /**
   * Skips a sequence
   * 
   * @param integer $num
   * @return void
   */
  protected function skip($num = 1)
  {
    $this->file->getStream($num); 
  }

  /**
   * Closes the File read
   * 
   * @return void
   */
  protected function closeStream()
  {
    $this->file->closeStream(); 
  }
}
