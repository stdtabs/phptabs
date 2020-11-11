<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Text;

use Exception;
use PhpTabs\Component\Exporter;
use PhpTabs\Component\Tablature;
use PhpTabs\Component\WriterInterface;
use PhpTabs\Music\Song;

class TextWriter implements WriterInterface
{
    private $content = '';

    public function __construct(Song $song)
    {
        if ($song->isEmpty()) {
            throw new Exception('Song is empty');
        }

        $tablature = new Tablature();
        $tablature->setSong($song);

        $exporter = new Exporter($tablature);

        $this->content = $exporter->toText();
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
