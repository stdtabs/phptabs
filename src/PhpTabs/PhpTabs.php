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

namespace PhpTabs;

use Exception;
use PhpTabs\Component\FileInput;
use PhpTabs\Component\Importer;
use PhpTabs\Component\InputStream;
use PhpTabs\Component\Reader;
use PhpTabs\Component\Tablature;

class PhpTabs
{
    /**
     * @var \PhpTabs\Component\Tablature A tablature container
     */
    private $tablature;

    /**
     * @param string $pathname A complete pathname
     */
    public function __construct(string $pathname = null)
    {
        // Create an emty tabs
        if (is_null($pathname)) {
            $this->setTablature(new Tablature());
        // It's a pathname
        } else {
            $file = new FileInput($pathname);
            $this->fromString(
                $file->getInputStream()
                     ->getStream($file->getInputStream()->getSize()),
                $file->getExtension()
            );
        }     
    }

    /**
     * Instanciate from string
     */
    public function fromString(string $string, string $extension = null): self
    {
        $reader = new Reader(
            new InputStream($string),
            $extension
        );

        return $this->setTablature($reader->getTablature());
    }

    /**
     * Get the tablature instance
     */
    public function getTablature(): Tablature
    {
        return $this->tablature;
    }

    /**
     * Set the tablature instance
     */
    public function setTablature(Tablature $tablature): self
    {
        $this->tablature = $tablature;

        return $this;
    }

    /**
     * Import a tablature from an array
     *
     * @param  array $data A set of data that has been exported
     */
    public function fromArray(array $data): self
    {
        $importer = new Importer($data);

        $this ->setTablature(new Tablature())
              ->setSong($importer->getSong());

        return $this;
    }

    /**
     * Get PhpTabs version
     */
    public function getVersion(): string
    {
        $filename = dirname(__DIR__, 2) . '/composer.json';

        IOFactory::checkFile($filename);

        $composer = json_decode(
            file_get_contents($filename)
        );

        if (json_last_error() === JSON_ERROR_NONE) {
            if (isset($composer->version) && is_string($composer->version)) {
                return $composer->version;
            }
        }

        return 'Undefined';
    }

    /**
     * Overloads with $tablature methods
     *
     * @param  string $name      A method name
     * @param  array  $arguments Optional arguments for the method
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        if (count($arguments) > 2) {
            $message = sprintf(
                '%s method does not support %d arguments',
                $name,
                count($arguments)
            );

            throw new Exception($message);
        }

        if (method_exists($this->tablature, $name)) {
            return $this->tablature->$name(...$arguments);
        }

        if (method_exists($this->tablature->getSong(), $name)) {
            return $this->tablature->getSong()->$name(...$arguments);
        }

        $message = sprintf(
            '%s method does not exist',
            $name
        );

        throw new Exception($message);
    }
}
