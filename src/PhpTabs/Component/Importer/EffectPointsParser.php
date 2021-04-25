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

final class EffectPointsParser extends ParserBase
{
    protected $required = ['points'];

    /**
     * Parse a color array
     * 
     * @param array $data
     */
    public function __construct(array $data, $effect)
    {
        $this->checkKeys($data, $this->required);

        foreach ($data['points'] as $point) {
            $this->checkKeys($point, ['position', 'value']);
            $effect->addPoint($point['position'], $point['value']);
        }

        $this->item = $effect;
    }
}
