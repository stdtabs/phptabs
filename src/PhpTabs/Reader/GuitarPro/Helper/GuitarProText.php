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

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Beat;
use PhpTabs\Music\Text;

final class GuitarProText extends AbstractReader
{
    /**
     * Read some text
     */
    public function readText(Beat $beat): void
    {
        $text = new Text();

        $text->setValue($this->reader->readStringByteSizeOfInteger());

        $beat->setText($text);
    }
}
