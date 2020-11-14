<?php

declare(strict_types = 1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component;

use Exception;

/**
 * A stream content
 */
class InputStream
{
    /**
     * @var int File size in bytes
     */
    private $size = 0;

    /**
     * @var int A file system pointer
     */
    private $handle = 0;

    /**
     * @var string file content
     */
    private $content = '';

    /**
     * @var string current segment
     */
    private $stream = '';

    /**
     * A string will be used as a stream content
     */
    public function __construct(string $content)
    {
        $this->setSize(strlen($content));
        $this->content = $content;
    }

    /**
     * @param int $size size of the stream (bytes)
     */
    private function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int size of the file (bytes)
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Streams a binary file
     *
     * @param int $bytes
     * @param int $offset
     *
     * @return null|string A file segment
     *
     * @throws Exception If asked position is larger than the file size
     */
    public function getStream(int $bytes = 1, int $offset = null): ?string
    {
        if ($this->handle + $bytes > $this->getSize()) {
            throw new Exception('Pointer');
        }

        // Nothing to read
        if ($bytes <= 0) {
            return null;
        }

        // Read $bytes with no offset
        if (null === $offset) {

            $this->stream = substr($this->content, $this->handle, $bytes);
            $this->handle += $bytes;

            return $this->stream;
        }

        // Moves pointer to $offset
        $this->handle += $offset;

        return $this->getStream($bytes);
    }

    /**
     * Returns the current position of the file read pointer
     *
     * @return int Position of the pointer.
     */
    public function getStreamPosition(): int
    {
        return $this->handle;
    }

    /**
     * Close stream
     */
    public function closeStream(): void
    {
        $this->handle = 0;
    }
}
