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
use PhpTabs\Component\Importer\ParserBase;
use PhpTabs\Music\Song;

class Importer extends ParserBase
{
    /**
     * @var \PhpTabs\Music\Song
     */
    protected $song;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->song = new Song();

        if (!isset($data['song'])) {
            throw new Exception('Invalid data: song key must be set');
        }

        $this->parseSong($data['song'], $this->song);
    }

    /**
     * Get built song object
     */
    public function getSong(): Song
    {
        return $this->song;
    }
}
