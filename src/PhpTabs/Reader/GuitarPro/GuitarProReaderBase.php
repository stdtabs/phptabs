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

use PhpTabs\Reader\GuitarPro\Helper\Factory;
use PhpTabs\Component\File;
use PhpTabs\Component\Log;

abstract class GuitarProReaderBase implements GuitarProReaderInterface
{
  /** @var int */
  private $versionIndex;

  /** @var string */
  private $version;

  /** @var string */
  private $parserName;

  /** @var \PhpTabs\Component\File */
  private $file;

  /**
   * @param \PhpTabs\Component\File $file An input file to read
   */
  public function __construct(File $file)
  {
    $this->file = $file;

    $xpt = explode('\\', get_class($this));

    $this->parserName = str_replace('Reader', '', $xpt[count($xpt)-1]);
  }

  /**
   * @return integer
   */
  public function getKeySignature()
  {
    return $this->keySignature;
  }

  /**
   * @param integer $value
   */
  public function setKeySignature($value)
  {
    return $this->keySignature = $value;
  }

  /**
   * @return boolean
   */
  public function getTripletFeel()
  {
    return $this->tripletFeel;
  }

  /**
   * Gets version
   * 
   * @return string Version
   */
  public function getVersion()
  {
    return $this->version;
  }

  /**
   * Gets version index
   * 
   * @return integer
   */
  public function getVersionIndex()
  {
    return $this->versionIndex;
  }

  /**
   * Reads Guitar Pro version
   */
  protected function readVersion()
  {
    if ($this->version === null)
    {
      $this->version = $this->readStringByte(30, 'UTF-8');

      Log::add($this->version);
    }
  }

  /**
   * Checks if dedicated readed supports the read version
   * 
   * @param string $version
   *
   * @return boolean true if supported, otherwise false
   */
  public function isSupportedVersion($version)
  {
    $versions = $this->getSupportedVersions();

    foreach ($versions as $k => $v)
    {
      if ($version == $v)
      {
        $this->versionIndex = $k;

        return true;
      }
    }

    return false;
  }

  /**
   * Reads a boolean
   * 
   * @return boolean
   */
  protected function readBoolean()
  {
    return ord($this->file->getStream()) == 1; 
  }

  /**
   * Reads a byte
   * 
   * @return byte
   */
  public function readByte()
  {
    return unpack('c', $this->file->getStream())[1];
  }

  /**
   * Reads an integer
   * 
   * @return integer
   */
  public function readInt()
  {
    $bytes = array();

    for ($i = 0; $i <= 3; $i++)
    {
      $bytes[$i] = unpack('C', $this->file->getStream())[1];
    }

    $or24 = $bytes[3];
    $ord24 = ($or24 & 127) << 24;
    if ($or24 >= 128) 
    {
      // negative number
      $ord24 = -abs((256 - $or24) << 24);
    }

    return $ord24 | (($bytes[2] & 0xff) << 16) | (($bytes[1] & 0xff) << 8) | ($bytes[0] & 0xff);
  }

  /**
   * Reads a string
   * 
   * @param integer $size Size to read in stream
   * @param integer|string $length Length to return or charset
   * @param string $charset
   *
   * @return string
   */
  protected function readString($size, $length = null, $charset = null)
  {
    if (null === $length && null === $charset)
    {
      return $this->readString($size, $size);
    }
    else if (is_string($length))
    {
      return $this->readString($size, $size, $length); // $length is charset
    }

    // Read brut content
    $size = $size > 0 ? $size : $length;
    $bytes = $this->file->getStream($size);

    if ($length >= 0 && $length <= $size)
    {
      // returns a subset
      return substr($bytes, 0, $length);
    }

    // returns all
    return $bytes;
  }

  /**
   * Reads string bytes
   *
   * @param integer $size
   * @param string $charset
   * 
   * @return string
   */
  public function readStringByte($size, $charset = 'UTF-8')
  { 
    return $this->readString($size, $this->readUnsignedByte(), $charset);
  }

  /**
   * Reads a sequence of an integer and string
   * 
   * @param string charset
   *
   * @return string
   */
  public function readStringByteSizeOfInteger($charset = 'UTF-8')
  {
    return $this->readStringByte(($this->readInt() - 1), $charset);
  }

  /**
   * Reads a string
   *
   * @param string $charset
   *
   * @return string
   */
  public function readStringInteger($charset = 'UTF-8')
  {
    return $this->readString($this->readInt(), $charset);
  }

  /**
   * Reads an unsigned byte
   * 
   * @return byte
   */
  public function readUnsignedByte()
  {
    return unpack('C', $this->file->getStream())[1];
  }

  /**
   * Skips a sequence
   * 
   * @param integer $num
   */
  public function skip($num = 1)
  {
    $this->file->getStream($num); 
  }

  /**
   * Closes the File read
   */
  protected function closeStream()
  {
    $this->file->closeStream(); 
  }

  /**
   * @param string $name
   * 
   * @return mixed
   */
  public function factory($name)
  {
    return (new Factory($this))->get($name, $this->parserName);
  }
}
