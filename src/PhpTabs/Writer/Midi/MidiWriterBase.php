<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Midi;

use PhpTabs\Component\WriterInterface;

class MidiWriterBase implements WriterInterface
{
  private $content;

  public function __construct()
  {
    $this->content = '';
  }

  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * @param int $integer
   */
  protected function writeInt($integer)
  {
    $this->content .= pack('N', $integer);
  }

  /**
   * @param int $integer
   */
  protected function writeShort($integer)
  {
    $this->content .= pack('n', $integer);
  }

  /**
   * @param array $bytes
   */
  protected function writeBytes(array $bytes)
  {
    foreach ($bytes as $byte) {
      $this->content .= pack('c', $byte);
    }
  }

  /**
   * @param array $bytes
   */
  protected function writeUnsignedBytes(array $bytes)
  {
    foreach ($bytes as $byte) {
      $this->content .= pack('C', $byte);
    }
  }

  /**
   * @param int $value
   * 
   * @param int $value
   */
  protected function writeVariableLengthQuantity($value)
  {
    $started = false;
    $length = 0;
    $data = intval(($value >> 21) & 0x7f);

    if ($data != 0) {
      $this->writeUnsignedBytes(array($data | 0x80));
      $length++;
      $started = true;
    }

    $data = intval(($value >> 14) & 0x7f);

    if ($data != 0 || $started) {
      $this->writeUnsignedBytes(array($data | 0x80));
      $length++;
      $started = true;
    }

    $data = intval(($value >> 7) & 0x7f);

    if ($data != 0 || $started) {
      $this->writeUnsignedBytes(array($data | 0x80));
      $length++;
    }

    $data = intval($value & 0x7f);

    $this->writeUnsignedBytes(array($data));
    $length++;

    return $length;
  }
}
