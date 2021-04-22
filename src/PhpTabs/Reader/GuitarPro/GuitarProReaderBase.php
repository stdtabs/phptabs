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

namespace PhpTabs\Reader\GuitarPro;

use PhpTabs\Reader\GuitarPro\Helper\Factory;
use PhpTabs\Component\InputStream;
use PhpTabs\Component\Log;

abstract class GuitarProReaderBase implements GuitarProReaderInterface
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
     * @var string
     */
    private $parserName;

    /**
     * @var \PhpTabs\Component\InputStream
     */
    private $file;

    /**
     * @param \PhpTabs\Component\InputStream $file An input file to read
     */
    public function __construct(InputStream $file)
    {
        $this->file = $file;

        $xpt = explode('\\', get_class($this));

        $this->parserName = str_replace('Reader', '', $xpt[count($xpt)-1]);
    }

    public function getKeySignature(): int
    {
        return $this->keySignature;
    }

    public function setKeySignature(int $value): int
    {
        return $this->keySignature = $value;
    }

    public function getTripletFeel(): int
    {
        return $this->tripletFeel;
    }

    /**
     * Get guitar pro version
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get version index
     */
    public function getVersionIndex(): int
    {
        return $this->versionIndex;
    }

    /**
     * Read Guitar Pro version
     */
    protected function readVersion(): void
    {
        if ($this->version === null) {
            $this->version = $this->readStringByte(30, 'UTF-8');

            Log::add($this->version);
        }
    }

    /**
     * Check if dedicated readed supports the read version
     */
    public function isSupportedVersion(string $version): bool
    {
        $versions = $this->getSupportedVersions();

        foreach ($versions as $k => $v) {
            if ($version == $v) {
                $this->versionIndex = $k;

                return true;
            }
        }

        return false;
    }

    /**
     * Read a boolean
     */
    protected function readBoolean(): bool
    {
        return ord($this->file->getStream()) == 1;
    }

    /**
     * Read a byte
     */
    public function readByte(): int
    {
        return unpack('c', $this->file->getStream())[1];
    }

    /**
     * Read an integer
     */
    public function readInt(): int
    {
        $bytes = [];

        for ($i = 0; $i <= 3; $i++) {
            $bytes[$i] = unpack('C', $this->file->getStream())[1];
        }

        $or24 = $bytes[3];
        $ord24 = ($or24 & 127) << 24;
        if ($or24 >= 128) {
            // negative number
            $ord24 = -abs((256 - $or24) << 24);
        }

        return $ord24 | (($bytes[2] & 0xff) << 16) | (($bytes[1] & 0xff) << 8) | ($bytes[0] & 0xff);
    }

    /**
     * Read a string
     *
     * @param int        $size    Size to read in stream
     * @param int|string $length  Length to return or charset
     * @param string         $charset
     */
    protected function readString(int $size, $length = null, $charset = null): string
    {
        if (null === $length && null === $charset) {
            return $this->readString($size, $size);
        } else if (is_string($length)) {
            return $this->readString($size, $size, $length); // $length is charset
        }

        // Read brut content
        $size = $size > 0 ? $size : $length;
        $bytes = $this->file->getStream($size);

        if (!is_null($bytes) && $length >= 0 && $length <= $size) {
            // returns a subset
            return substr($bytes, 0, $length);
        }

        // returns all
        return (string)$bytes;
    }

    /**
     * Read string bytes
     */
    public function readStringByte(int $size, string $charset = 'UTF-8'): string
    {
        return $this->readString($size, $this->readUnsignedByte(), $charset);
    }

    /**
     * Read a sequence of an integer and string
     */
    public function readStringByteSizeOfInteger(string $charset = 'UTF-8'): string
    {
        return $this->readStringByte(($this->readInt() - 1), $charset);
    }

    /**
     * Reads a string
     */
    public function readStringInteger(string $charset = 'UTF-8'): string
    {
        return $this->readString($this->readInt(), $charset);
    }

    /**
     * Reads an unsigned byte
     *
     * @return int
     */
    public function readUnsignedByte(): int
    {
        return unpack('C', $this->file->getStream())[1];
    }

    /**
     * Skips a sequence
     *
     * @param int $num
     */
    public function skip(int $num = 1): void
    {
        $this->file->getStream($num);
    }

    /**
     * Closes the File read
     */
    protected function closeStream(): void
    {
        $this->file->closeStream();
    }

    /**
     * Get a subparser
     *
     * @return mixed
     */
    public function factory(string $name)
    {
        return (new Factory($this))->get($name, $this->parserName);
    }
}
