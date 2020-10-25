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

namespace PhpTabs\Writer\Midi;

class MidiSequenceHelper
{
    private $measureHeaderHelpers;
    private $sequence;

    public function __construct(MidiSequenceHandler $sequence)
    {
        $this->sequence = $sequence;
        $this->measureHeaderHelpers = [];
    }

    public function getSequence(): MidiSequenceHandler
    {
        return $this->sequence;
    }

    public function addMeasureHelper(MidiMeasureHelper $helper): void
    {
        $this->measureHeaderHelpers[] = $helper;
    }

    public function getMeasureHelpers(): array
    {
        return $this->measureHeaderHelpers;
    }

    public function getMeasureHelper(int $index): MidiMeasureHelper
    {
        return $this->measureHeaderHelpers[$index];
    }
}
