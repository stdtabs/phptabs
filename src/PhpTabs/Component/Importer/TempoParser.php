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

use PhpTabs\Music\Tempo;

final class TempoParser extends ParserBase
{
    /**
     * Parse a tempo array
     */
    public function __construct(int $data)
    {
        $tempo = new Tempo();
        $tempo->setValue($data);

        $this->item = $tempo;
    }
}
