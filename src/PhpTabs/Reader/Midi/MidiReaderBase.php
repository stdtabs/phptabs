<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\Midi;

use Exception;
use PhpTabs\Component\File;

abstract class MidiReaderBase implements MidiReaderInterface
{
    /**
     * @var \PhpTabs\Component\File
     */
    private $file;

    /**
     * @param \PhpTabs\Component\File $file input file to read
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Read a 32 bit integer big endian
     */
    protected function readInt(): int
    {
        $bytes = $this->readBytesBigEndian(4);

        return ($bytes[3] & 0xff)
            | (($bytes[2] & 0xff) << 8)
            | (($bytes[1] & 0xff) << 16)
            | (($bytes[0] & 0xff) << 24);
    }

    /**
     * Read a 16 bit integer big endian
     */
    protected function readShort(): int
    {
        $bytes = $this->readBytesBigEndian(2);

        return (($bytes[0] & 0xff) << 8) | ($bytes[1] & 0xff);
    }

    /**
     * Read an unsigned 16 bit integer big endian
     */
    protected function readUnsignedShort(): int
    {
        $bytes = $this->readBytesBigEndian(2);

        return (($bytes[0] & 0x7f) << 8) | ($bytes[1] & 0xff);
    }

    /**
     * @throws \Exception if variable length is not readable
     */
    public function readVariableLengthQuantity(MidiTrackReaderHelper $helper): int
    {
        $count = 0;
        $value = 0;

        while ($count < 4) {
            $data = $this->readUnsignedByte();
            $helper->remainingBytes--;
            $count++;
            $value <<= 7;
            $value |= ($data & 0x7f);
            if ($data < 128) {
                return $value;
            }
        }

        throw new Exception("Not a MIDI file: unterminated variable-length quantity");
    }

    /**
     * Read an unsigned byte
     */
    protected function readUnsignedByte(): int
    {
        return unpack('C', $this->file->getStream())[1];
    }

    /**
     * Skip a sequence
     */
    protected function skip(int $num = 1): void
    {
        $this->file->getStream($num);
    }

    /**
     * Read and return an array of bytes
     */
    protected function readBytesBigEndian(int $num = 1): array
    {
        $bytes = array();

        for ($i = 0; $i < $num; $i++) {
            $bytes[$i] = ord($this->file->getStream());
        }

        return $bytes;
    }

    /**
     * Closes \PhpTabs\Component\File read process
     */
    protected function closeStream(): void
    {
        $this->file->closeStream();
    }
}
