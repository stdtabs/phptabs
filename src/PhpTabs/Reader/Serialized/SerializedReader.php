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

namespace PhpTabs\Reader\Serialized;

use Exception;
use PhpTabs\Component\InputStream;
use PhpTabs\Component\ReaderInterface;
use PhpTabs\Component\Tablature;
use PhpTabs\Music\Song;
use PhpTabs\IOFactory;

class SerializedReader implements ReaderInterface
{
    public function __construct(InputStream $file)
    {
        $song = new Song();

        $data = @unserialize( // Skip warning
            $file->getStream($file->getSize()),
            ['allowed_classes' => false]
        );

        // unserialize failed
        if ($data === false) {
            $message = sprintf('UNSERIALIZE_FAILURE');

            throw new Exception($message);
        }

        $this->setTablature(IOFactory::fromArray($data)->getSong());
    }

    /**
     * {@inheritdoc}
     */
    public function getTablature(): Tablature
    {
        return isset($this->tablature)
            ? $this->tablature
            : new Tablature();
    }

    /**
     * Initialize Tablature with read Song
     */
    private function setTablature(Song $song): void
    {
        if (!isset($this->tablature)) {
            $this->tablature = new Tablature();
        }

        $this->tablature->setSong($song);
        $this->tablature->setFormat('json');
    }
}
