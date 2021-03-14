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
use PhpTabs\Music\Chord;

class GuitarPro3Chord extends AbstractReader
{
    /**
     * Read Chord informations
     */
    public function readChord($strings, Beat $beat): void
    {
        $chord = new Chord($strings);
        $header = $this->reader->readUnsignedByte();

        if (($header & 0x01) == 0) {
            $chord->setName($this->reader->readStringByteSizeOfInteger());
            $chord->setFirstFret($this->reader->readInt());

            if ($chord->getFirstFret() != 0) {
                $this->readStrings($chord);
            }
        } else {
            $this->reader->skip(25);
            $chord->setName($this->reader->readStringByte(34));
            $chord->setFirstFret($this->reader->readInt());

            $this->readStrings($chord);

            $this->reader->skip(36);
        }

        if ($chord->countNotes() > 0) {
            $beat->setChord($chord);
        }
    }

    private function readStrings(Chord $chord): void
    {
        for ($i = 0; $i < 6; $i++) {
            $fret = $this->reader->readInt();

            if ($i < $chord->countStrings()) {
                $chord->addFretValue($i, $fret);
            }
        }
    }
}
