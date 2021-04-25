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

use PhpTabs\Music\Marker;

final class MarkerParser extends ParserBase
{
    protected $required = ['measure', 'title', 'color'];

    /**
     * Parse a marker array
     */
    public function __construct(array $data)
    {
        $this->checkKeys($data, $this->required);

        $marker = new Marker();

        if (!is_null($data['title'])) {
            $marker->setMeasure($data['measure']);
            $marker->setTitle($data['title']);
            $marker->setColor(
                $this->parseColor($data['color'])
            );
        }

        $this->item = $marker;
    }
}
