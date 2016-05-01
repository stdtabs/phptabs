<?php

namespace PhpTabs\Reader\GuitarPro;

use PhpTabs\Component\File;
use PhpTabs\Component\Log;
use PhpTabs\Model\Song;


abstract class GuitarProReaderBase
{
  /**
   * @var int
   */
  private $versionIndex;

  /**
   * @var string
   */
  private $version;

  /**
   * @var array
   */
  private $versions;

  /**
   * @var File
   */
  private $file;

  /**
   * Reader constructor
   *
   * @param File $file input file to read
   */
  public function __construct(File $file)
  {
    $this->file = $file;
  }

  /**
   * Get read version
   * 
   * @return string Version
   */
  public function getVersion()
  {
    return $this->version;
  }
 
  /**
   * Reads Guitar Pro version
   * 
   * @return void
   */
  protected function readVersion()
  {
    if($this->version == null)
    {
      $this->version = $this->readStringByte(30, 'UTF-8');

      Log::add($this->version);
    }
  }

  /**
   * Checks if dedicated readed supports the read version
   * 
   * @return boolean true if supported, otherwise false
   */
  public function isSupportedVersion($version)
  {
    $versions = $this->getSupportedVersions();

    foreach($versions as $k => $v)
    {
      if($this->version == $v)
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
    return $this->file->getStream() == 1; 
  }

  /**
   * Reads a byte
   * 
   * @return byte
   */
  protected function readByte()
  {
    return ord($this->file->getStream()) & 127;
  }

  /**
   * Reads an integer
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
    $bytes = $this->file->getStream($size);

    if ($length>=0 && $length<=$size)
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
   * @return string
   */
  protected function readStringByte($size, $charset = null)
  { 
    return $this->readString($size, $this->readUnsignedByte(), $charset);
  }

  /**
   * Reads a sequence of an integer and string
   * 
   * @param string charset
   * @return string
   */
  protected function readStringByteSizeOfInteger($charset = 'UTF-8')
  {
    return $this->readStringByte(($this->readInt() - 1), $charset);
  }

  /**
   * Reads a string
   *
   * @param string $charset
   * @return string
   */
  protected function readStringInteger($charset = 'UTF-8')
  {
    return $this->readString($this->readInt());
  }

  /**
   * Reads an unsigned byte
   * 
   * @return byte
   */
  protected function readUnsignedByte()
  {
    return (ord($this->file->getStream()) & 0xff);
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
