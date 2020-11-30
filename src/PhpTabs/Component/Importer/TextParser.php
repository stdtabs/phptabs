<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use PhpTabs\Music\Text;

class TextParser extends ParserBase
{
    protected $required = ['value'];

    /**
     * Parse a text array
     */
    public function __construct(array $data)
    {
        $this->checkKeys($data, $this->required);

        $text = new Text();

        if (!is_null($data['value'])) {
            $text->setValue($data['value']);
        }

        $this->item = $text;
    }
}
