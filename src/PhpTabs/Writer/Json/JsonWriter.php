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

namespace PhpTabs\Writer\Json;

use Exception;
use PhpTabs\Component\Exporter;
use PhpTabs\Component\Tablature;
use PhpTabs\Component\WriterInterface;
use PhpTabs\Music\Song;

final class JsonWriter implements WriterInterface
{
    /**
     * @var string
     */
    private $content = '';

    public function __construct(Song $song)
    {
        if ($song->isEmpty()) {
            throw new Exception('Song is empty');
        }

        $tablature = new Tablature();
        $tablature->setSong($song);

        $exporter = new Exporter($tablature);

        $this->content = $exporter->toJson();
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
