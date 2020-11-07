<?php

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
use PhpTabs\IOFactory;

/**
 * File wrapper class
 */
class FileInput
{
    /**
     * @var string Path to the file
     */
    private $path;

    /**
     * @var string dirname of the file
     */
    private $dirname;

    /**
     * @var string extension of the file
     */
    private $extension;

    /**
     * @var string basename of the file
     */
    private $basename;

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
     * @param string $path Path to the file
     */
    public function __construct(string $path)
    {
        $this->setPath($path);

        IOFactory::checkFile($path);

        $informations = pathinfo($path);

        $this->setDirname(isset($informations['dirname']) ? $informations['dirname'] : '');
        $this->setBasename(isset($informations['basename']) ? $informations['basename'] : '');
        $this->setExtension(isset($informations['extension']) ? $informations['extension'] : '');
        $this->setSize(filesize($path));
        $this->content = file_get_contents($path);
    }

    /**
     * @param string $path Path to the file passed as a parameter
     */
    private function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string Path to the file as it was passed as a parameter
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $name Directory of the path
     */
    private function setDirname(string $name): void
    {
        $this->dirname = $name;
    }

    /**
     * @return string Directory of the path
     */
    public function getDirname(): string
    {
        return $this->dirname;
    }

    /**
     * @param string $name Extension of the path
     */
    private function setExtension(string $name): void
    {
        $this->extension = $name;
    }

    /**
     * @return string Extension of the path
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $name Basename of the path
     */
    private function setBasename(string $name): void
    {
        $this->basename = $name;
    }

    /**
     * @return string Basename of the path
     */
    public function getBasename(): string
    {
        return $this->basename;
    }

    /**
     * @param int $size size of the file (bytes)
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
