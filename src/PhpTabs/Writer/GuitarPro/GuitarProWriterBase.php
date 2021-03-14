<?php

declare(strict_types=1);

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
use PhpTabs\Music\Color;
use PhpTabs\Music\Duration;
use PhpTabs\Music\Song;
use PhpTabs\Music\Stroke;
use PhpTabs\Share\ChannelRoute;
use PhpTabs\Share\ChannelRouter;
use PhpTabs\Share\ChannelRouterConfigurator;

abstract class GuitarProWriterBase implements WriterInterface
{
    private $content = '';
    private $name;
    private $writers = [];

    /**
     * @var ChannelRouter
     */
    protected $channelRouter;

    public function __construct()
    {
        $this->name = str_replace(
            __NAMESPACE__ . '\\',
            '',
            get_class($this)
        );

        $this->channelRouter = new ChannelRouter();
    }

    public function writeColor(Color $color): void
    {
        $this->writeUnsignedByte($color->getR());
        $this->writeUnsignedByte($color->getG());
        $this->writeUnsignedByte($color->getB());
        $this->writeByte(0);
    }

    public function parseDuration(Duration $duration): int
    {
        switch ($duration->getValue()) {
            case Duration::WHOLE:
                return -2;
            case Duration::HALF:
                return -1;
            case Duration::QUARTER:
                return 0;
            case Duration::EIGHTH:
                return 1;
            case Duration::SIXTEENTH:
                return 2;
            case Duration::THIRTY_SECOND:
                return 3;
            case Duration::SIXTY_FOURTH:
                return 4;
        }

        return 0;
    }

    public function toStrokeValue(Stroke $stroke): int
    {
        switch ($stroke->getValue()) {
            case Duration::SIXTY_FOURTH:
                return 2;
            case Duration::THIRTY_SECOND:
                return 3;
            case Duration::SIXTEENTH:
                return 4;
            case Duration::EIGHTH:
                return 5;
            case Duration::QUARTER:
                return 6;
            default:
                return 2;
        }
    }

    /**
     * Get a dedicated writer
     *
     * @return mixed
     */
    public function getWriter(string $name)
    {
        if (!isset($this->writers[$name])) {
            $classname = __NAMESPACE__ . '\\Writers\\' . ucfirst($name);
            $this->writers[$name] = new $classname($this);
        }

        return $this->writers[$name];
    }

    /**
     * Get name
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getChannelRoute(int $channelId): ChannelRoute
    {
        $channelRoute = $this->channelRouter->getRoute($channelId);

        if (null === $channelRoute) {
            $channelRoute = new ChannelRoute(ChannelRoute::NULL_VALUE);
            $channelRoute->setChannel1(15);
            $channelRoute->setChannel2(15);
        }

        return $channelRoute;
    }

    protected function configureChannelRouter(Song $song): void
    {
        $this->channelRouter = new ChannelRouter();

        $routerConfigurator = new ChannelRouterConfigurator($this->channelRouter);
        $routerConfigurator->configureRouter($song->getChannels());
    }

    public function skipBytes(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->writeByte(0);
        }
    }

    public function writeBoolean(bool $boolean): void
    {
        $this->writeByte($boolean ? 1 : 0);
    }

    public function writeByte($byte): void
    {
        $this->content .= pack('c', $byte);
    }

    public function writeBytes(array $bytes): void
    {
        array_walk(
            $bytes, function ($byte) {
                $this->writeByte($byte);
            }
        );
    }

    public function writeInt(int $integer): void
    {
        $this->content .= pack('V', $integer);
    }

    public function writeStringByteSizeOfInteger(string $string): void
    {
        $this->writeInt(strlen($string) + 1);
        $this->writeStringByte($string, strlen($string));
    }

    public function writeString(string $bytes, int $maximumLength): void
    {
        $length = $maximumLength == 0 || $maximumLength > strlen($bytes)
            ? strlen($bytes)
            : $maximumLength;

        for ($i = 0 ; $i < $length; $i++) {
            $this->content .= $bytes[$i];
        }
    }

    public function writeStringInteger(string $string): void
    {
        $this->writeInt(strlen($string));
        $this->writeString($string, 0);
    }

    public function writeStringByte(string $string, int $size): void
    {
        $this->writeByte(
            $size == 0 || $size > strlen($string)
                ? strlen($string)
                : $size
        );

        $this->writeString($string, $size);
        $this->skipBytes($size - strlen($string));
    }

    public function writeUnsignedByte(int $byte): void
    {
        $this->content .= pack('C', $byte);
    }
}
