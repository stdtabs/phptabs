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

namespace PhpTabs\Component\Importer;

use PhpTabs\Music\Lyric;

final class LyricsParser extends ParserBase
{
    /**
     * @var array<string>
     */
    protected $required = ['from', 'lyrics'];

    /**
     * Parse a lyrics array
     */
    public function __construct(array $data)
    {
        $this->checkKeys($data, $this->required);

        $lyric = new Lyric();

        if (!is_null($data['lyrics'])) {
            $lyric->setFrom($data['from']);
            $lyric->setLyrics($data['lyrics']);
        }

        $this->item = $lyric;
    }
}
