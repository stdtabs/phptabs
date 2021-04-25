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

namespace PhpTabs\Component;

use Exception;
use PhpTabs\Reader\GuitarPro\GuitarPro3Reader;
use PhpTabs\Reader\GuitarPro\GuitarPro4Reader;
use PhpTabs\Reader\GuitarPro\GuitarPro5Reader;
use PhpTabs\Reader\Json\JsonReader;
use PhpTabs\Reader\Midi\MidiReader;
use PhpTabs\Reader\Serialized\SerializedReader;

final class Reader
{
    /**
     * @var ReaderInterface bridge
     */
    private $bridge;

    /**
     * @var array List of available extensions and their bridge
     */
    private $extensions = [
        'gp3'   => GuitarPro3Reader::class,
        'gp4'   => GuitarPro4Reader::class,
        'gp5'   => GuitarPro5Reader::class,
        'json'  => JsonReader::class,
        'mid'   => MidiReader::class,
        'midi'  => MidiReader::class,
        'ser'   => SerializedReader::class,
    ];

    /**
     * Try to load the dedicated parser.
     *
     * @throws \Exception If file format is not supported
     */
    public function __construct(InputStream $input, string $extension)
    {
        // Bridge is not defined
        if (!isset($this->extensions[$extension])) {
            $message = sprintf(
                'No reader has been found for "%s" type of file',
                $extension
            );

            throw new Exception($message);
        }

        $this->bridge = new $this->extensions[$extension]($input);

        $this->bridge->getTablature()->setFormat($extension);
    }

    /**
     * Get bridge tablature instance
     */
    public function getTablature(): Tablature
    {
        return $this->bridge->getTablature();
    }
}
