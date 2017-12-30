<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro;

use PhpTabs\Component\WriterInterface;
use PhpTabs\Model\ChannelRouter;
use PhpTabs\Model\ChannelRouterConfigurator;
use PhpTabs\Music\Song;

abstract class GuitarProWriterBase implements WriterInterface
{
  private $content;
  private $writers = [];

  public function __construct()
  {
    $this->content = '';
  }

  /**
   * Get a dedicated writer
   * 
   * @param  string $name
   * @return mixed
   */
  public function getWriter($name)
  {
    if (!isset($this->writers[$name])) {
      $classname = __NAMESPACE__ . '\\Writers\\' . ucfirst($name);
      $this->writers[$name] = new $classname($this);
    }

    return $this->writers[$name];
  }

  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * @param  int $channelId
   * @return \PhpTabs\Music\ChannelRoute
   */
  public function getChannelRoute($channelId)
  {
    $channelRoute = $this->channelRouter->getRoute($channelId);

    if (null === $channelRoute) {
      $channelRoute = new ChannelRoute(ChannelRoute::NULL_VALUE);
      $channelRoute->setChannel1(15);
      $channelRoute->setChannel2(15);
    }

    return $channelRoute;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  protected function configureChannelRouter(Song $song)
  {
    $this->channelRouter = new ChannelRouter();

    $routerConfigurator = new ChannelRouterConfigurator($this->channelRouter);
    $routerConfigurator->configureRouter($song->getChannels());
  }

  /**
   * @param int $count
   */
  public function skipBytes($count)
  {
    for ($i = 0; $i < $count; $i++) {
      $this->writeByte(0);
    }
  }

  /**
   * @param bool $boolean
   */
  public function writeBoolean($boolean)
  {
    $this->writeByte($boolean ? 1 : 0);
  }

  /**
   * @param byte $byte
   */
  public function writeByte($byte)
  {
    $this->content .= pack('c', $byte);
  }

  /**
   * @param array $bytes
   */
  public function writeBytes(array $bytes)
  {
    array_walk($bytes, function ($byte) {
      $this->writeByte($byte);
    });
  }

  /**
   * @param int $integer
   */
  public function writeInt($integer)
  {
    $this->content .= pack('V', $integer);
  }

  /**
   * @param string $string
   */
  public function writeStringByteSizeOfInteger($string)
  {
    $this->writeInt(strlen($string) + 1);
    $this->writeStringByte($string, strlen($string));
  }

  /**
   * @param string $bytes
   * @param int $maximumLength
   */
  public function writeString($bytes, $maximumLength)
  {
    $length = $maximumLength == 0 || $maximumLength > strlen($bytes)
      ? strlen($bytes) : $maximumLength;

    for ($i = 0 ; $i < $length; $i++) {
      $this->content .= $bytes[$i];
    }
  }

  /**
   * @param string $string
   */
  public function writeStringInteger($string)
  {
    $this->writeInt(strlen($string));
    $this->writeString($string, 0);
  }

  /**
   * @param string $string
   * @param int $size
   */
  public function writeStringByte($string, $size)
  {
    $this->writeByte($size == 0 || $size > strlen($string) 
      ? strlen($string) : $size
    );

    $this->writeString($string , $size);
    $this->skipBytes($size - strlen($string));
  }

  /**
   * @param byte $byte
   */
  public function writeUnsignedByte($byte)
  {
    $this->content .= pack('C', $byte);
  }
}
