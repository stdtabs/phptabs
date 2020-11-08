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

class Reader
{
    /**
     * @var Tablature object
     */
    private $tablature;

    /**
     * @var ReaderInterface bridge
     */
    private $bridge;

    /**
     * @var array List of extensions
     */
    private $extensions = [
        'gp3'   => 'PhpTabs\\Reader\\GuitarPro\\GuitarPro3Reader',
        'gp4'   => 'PhpTabs\\Reader\\GuitarPro\\GuitarPro4Reader',
        'gp5'   => 'PhpTabs\\Reader\\GuitarPro\\GuitarPro5Reader',
        'json'  => 'PhpTabs\\Reader\\Json\\JsonReader',
        'mid'   => 'PhpTabs\\Reader\\Midi\\MidiReader',
        'midi'  => 'PhpTabs\\Reader\\Midi\\MidiReader',
    ];

    /**
     * Instanciates tablature container and try to load the dedicated
     * parser.
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

        $this->tablature = new Tablature();

        $name = $this->extensions[$extension];

        $this->bridge = new $name($input);

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
