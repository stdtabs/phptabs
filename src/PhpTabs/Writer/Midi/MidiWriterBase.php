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

namespace PhpTabs\Writer\Midi;

use PhpTabs\Component\WriterInterface;

abstract class MidiWriterBase implements WriterInterface
{
    /**
     * @var string
     */
    private $content = '';

    public function getContent(): string
    {
        return $this->content;
    }

    protected function writeInt(int $integer): void
    {
        $this->content .= pack('N', $integer);
    }

    protected function writeShort(int $integer): void
    {
        $this->content .= pack('n', $integer);
    }

    /**
     * @param array<int> $bytes
     */
    protected function writeBytes(array $bytes): void
    {
        foreach ($bytes as $byte) {
            $this->content .= pack('c', $byte);
        }
    }

    /**
     * @param array<int> $bytes
     */
    protected function writeUnsignedBytes(array $bytes): void
    {
        foreach ($bytes as $byte) {
            $this->content .= pack('C', $byte);
        }
    }

    protected function writeVariableLengthQuantity(int $value, ?string $out = null): int
    {
        $started = false;
        $length = 0;
        $data = ($value >> 21) & 0x7f;

        if ($data !== 0) {
            if ($out !== null) {
                $this->writeUnsignedBytes([$data | 0x80]);
            }
            $length++;
            $started = true;
        }

        $data = (($value >> 14) & 0x7f);

        if ($data !== 0 || $started) {
            if ($out !== null) {
                $this->writeUnsignedBytes([$data | 0x80]);
            }
            $length++;
            $started = true;
        }

        $data = ($value >> 7) & 0x7f;

        if ($data !== 0 || $started) {
            if ($out !== null) {
                $this->writeUnsignedBytes([$data | 0x80]);
            }
            $length++;
        }

        $data = $value & 0x7f;
        if ($out !== null) {
            $this->writeUnsignedBytes([$data]);
        }
        $length++;

        return $length;
    }
}
