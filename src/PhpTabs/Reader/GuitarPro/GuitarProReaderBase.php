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


  public function __construct(File $file)
  {
    $this->file = $file;
  }

  public function getVersion()
  {
    return $this->version;
  }
 
  /**
   * Read Guitar Pro version
   */
  protected function readVersion()
  {
    if($this->version == null)
    {
      $this->version = $this->readStringByte(30, 'UTF-8');

      Log::add($this->version);
    }
  }


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


  protected function readBoolean()
  {
    return $this->file->getStream() == 1; 
  }

  protected function readByte()
  {
    return ord($this->file->getStream()) & 0xff;
  }

  protected function readInt()
  {
    $bytes = array();

    for($i=0; $i<=3; $i++)
    {
      $bytes[$i] = ord($this->file->getStream());
    }

    return (($bytes[3] & 0xf) << 24) | (($bytes[2] & 0xf) << 16) | (($bytes[1] & 0xf) << 8) | ($bytes[0] & 0xff);
  }


  /**
   * @param int $size Size to read in stream
   * @param int $length Length to return
   * @param string $charset
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
   * @param int $size
   * @param string $charset
   */
  protected function readStringByte($size, $charset = null)
  { 
    return $this->readString($size, $this->readUnsignedByte(), $charset);
  }

  protected function readStringByteSizeOfInteger($charset = 'UTF-8')
  {
    return $this->readStringByte(($this->readInt() - 1), $charset);
  }

  protected function readStringInteger($charset = 'UTF-8')
  {

    return $this->readString($this->readInt());
  }


  protected function readUnsignedByte()
  {
    return (ord($this->file->getStream()) & 0xff);
  }

  protected function skip($num = 1)
  {
    $this->file->getStream($num); 
  }

  protected function closeStream()
  {
    $this->file->closeStream(); 
  }
}
