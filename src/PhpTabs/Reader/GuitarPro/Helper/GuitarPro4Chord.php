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

final class GuitarPro4Chord extends AbstractReader
{
    /**
     * Reads Chord informations
     */
    public function readChord(int $strings, Beat $beat): void
    {
        $chord = new Chord($strings);

        if (($this->reader->readUnsignedByte() & 0x01) === 0) {
            $chord->setName($this->reader->readStringByteSizeOfInteger());
            $chord->setFirstFret($this->reader->readInt());

            if ($chord->getFirstFret() !== 0) {
                for ($i = 0; $i < 6; $i++) {
                    $this->readFret($chord, $i);
                }
            }
        } else {
            $this->reader->skip(16);
            $chord->setName($this->reader->readStringByte(21));
            $this->reader->skip(4);
            $chord->setFirstFret($this->reader->readInt());

            for ($i = 0; $i < 7; $i++) {
                $this->readFret($chord, $i);
            }

            $this->reader->skip(32);
        }

        if ($chord->countNotes() > 0) {
            $beat->setChord($chord);
        }
    }

    private function readFret(Chord $chord, int $index): void
    {
        $fret = $this->reader->readInt();

        if ($index < $chord->countStrings()) {
            $chord->addFretValue($index, $fret);
        }
    }
}
