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

namespace PhpTabs\Reader\Midi;

/**
 * Midi track helper
 */
class MidiTrackReaderHelper
{
    public $ticks = 0;
    public $remainingBytes;
    public $runningStatusByte;

    public function __construct(int $ticks, int $remainingBytes, int $runningStatusByte)
    {
        $this->ticks = $ticks;
        $this->remainingBytes = $remainingBytes;
        $this->runningStatusByte = $runningStatusByte;
    }
}
