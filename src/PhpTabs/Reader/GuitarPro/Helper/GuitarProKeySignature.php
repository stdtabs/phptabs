<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\GuitarPro\Helper;

class GuitarProKeySignature extends AbstractReader
{
    /**
     * Read the key signature as an integer
     *
     * 0: C 1: G, -1: F
     */
    public function readKeySignature(): int
    {
        $keySignature = $this->reader->readByte();

        if ($keySignature < 0) {
            $keySignature = 7 - $keySignature;
        }

        return $keySignature;
    }
}
