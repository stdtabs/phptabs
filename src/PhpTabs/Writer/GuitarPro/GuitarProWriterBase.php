<?php

namespace PhpTabs\Writer\GuitarPro;

use PhpTabs\Component\WriterInterface;

use PhpTabs\Model\Song;
use PhpTabs\Model\ChannelRouter;
use PhpTabs\Model\ChannelRouterConfigurator;
use PhpTabs\Model\Helper;

abstract class GuitarProWriterBase extends Helper implements WriterInterface
{
  private $content;

  public function __construct()
  {
    $this->content = '';
  }

  public function getContent()
  {
    return $this->content;
  }

  protected function getChannelRoute($channelId)
  {
    $channelRoute = $this->channelRouter->getRoute($channelId);

    if(null === $channelRoute)
    {
      $channelRoute = new ChannelRoute(ChannelRoute::NULL_VALUE);
      $channelRoute->setChannel1(15);
      $channelRoute->setChannel2(15);
    }

    return $channelRoute;
  }

  protected function configureChannelRouter(Song $song)
  {
    $this->channelRouter = new ChannelRouter();

    $routerConfigurator = new ChannelRouterConfigurator($this->channelRouter);
    $routerConfigurator->configureRouter($song->getChannels());
  }

  protected function skipBytes($count)
  {
    for($i = 0; $i < $count; $i++)
    {
      $this->writeByte(0);
    }
  }

  protected function writeBoolean($boolean)
  {
    $this->writeByte($boolean == true ? 1 : 0);
  }

  protected function writeByte($byte)
  {
    $this->content .= pack('c', $byte);
  }

  protected function writeBytes(array $bytes)
  {
    foreach($bytes as $byte)
    {
      $this->content .= pack('c', $byte);
    }
  }

  protected function writeInt($integer)
  {
    $this->content .= pack('V', $integer);
  }

  protected function writeStringByteSizeOfInteger($string)
  {
    $this->writeInt(strlen($string) + 1);
    $this->writeStringByte($string, strlen($string));
  }

  protected function writeString($bytes, $maximumLength)
  {
    $length = $maximumLength == 0 || $maximumLength > strlen($bytes)
      ? strlen($bytes) : $maximumLength;

    for($i = 0 ; $i < $length; $i++)
    {
      $this->content .= $bytes[$i];
    }
  }

  protected function writeStringInteger($string)
  {
    $this->writeInt(strlen($string));
    $this->writeString($string, 0);
  }

  protected function writeStringByte($string, $size)
  {
    $this->writeByte($size == 0 || $size > strlen($string) 
      ? strlen($string) : $size
    );

    $this->writeString($string , $size);
    $this->skipBytes($size - strlen($string));
  }

  protected function writeUnsignedByte($byte)
  {
    $this->content .= pack('C', $byte);
  }
}
