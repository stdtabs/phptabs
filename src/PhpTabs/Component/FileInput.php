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
     * @var InputStream file content
     */
    private $content = '';

    /**
     * @param string $path Path to the file
     */
    public function __construct(string $path)
    {
        IOFactory::checkFile($path);

        $this->setPath($path);
        $informations = pathinfo($path);

        $this->setDirname(isset($informations['dirname']) ? $informations['dirname'] : '');
        $this->setBasename(isset($informations['basename']) ? $informations['basename'] : '');
        $this->setExtension(isset($informations['extension']) ? $informations['extension'] : '');
        $this->content = new InputStream(file_get_contents($path));
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
     * Get content as an input stream
     */
    public function getInputStream(): InputStream
    {
        return $this->content;
    }
}
