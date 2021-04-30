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

use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\TimeSignature;

final class GuitarPro5MeasureHeader extends AbstractReader
{
    /**
     * Read a measure header
     */
    public function readMeasureHeader(int $index, TimeSignature $timeSignature, int $tempoValue = 120): MeasureHeader
    {
        $flags = $this->reader->readUnsignedByte();
        $header = new MeasureHeader();
        $header->setNumber($index + 1);
        $header->setStart(0);
        $header->getTempo()->setValue($tempoValue);
        $header->setRepeatOpen(intval(($flags & 0x04) !== 0));

        if (($flags & 0x01) !== 0) {
            $timeSignature->setNumerator($this->reader->readByte());
        }

        if (($flags & 0x02) !== 0) {
            $timeSignature->getDenominator()->setValue($this->reader->readByte());
        }

        $header->getTimeSignature()->copyFrom($timeSignature);

        if (($flags & 0x08) !== 0) {
            $header->setRepeatClose(($this->reader->readByte() & 0xff) - 1);
        }

        if (($flags & 0x20) !== 0) {
            $header->setMarker($this->reader->factory('GuitarProMarker')->readMarker($header->getNumber()));
        }

        if (($flags & 0x10) !== 0) {
            $header->setRepeatAlternative($this->reader->readUnsignedByte());
        }

        if (($flags & 0x40) !== 0) {
            $this->reader->setKeySignature($this->reader->factory('GuitarProKeySignature')->readKeySignature());
            $this->reader->skip(1);
        }

        if (($flags & 0x01) !== 0 || ($flags & 0x02) !== 0) {
            $this->reader->skip(4);
        }

        if (($flags & 0x10) === 0) {
            $this->reader->skip(1);
        }

        $tripletFeel = $this->reader->readByte();

        if ($tripletFeel === 1) {
            $header->setTripletFeel(MeasureHeader::TRIPLET_FEEL_EIGHTH);
        } elseif ($tripletFeel === 2) {
            $header->setTripletFeel(MeasureHeader::TRIPLET_FEEL_SIXTEENTH);
        } else {
            $header->setTripletFeel(MeasureHeader::TRIPLET_FEEL_NONE);
        }

        return $header;
    }
}
