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

use PhpTabs\Component\ReaderInterface;

/**
 * Interface for Midi Reader classes
 */
interface MidiReaderInterface extends ReaderInterface
{
    public const HEADER_LENGTH = 6;
    public const HEADER_MAGIC = 0x4d546864;
    public const TRACK_MAGIC = 0x4d54726b;

    /**
     * Sequence
     */
    public const PPQ = 0.0;
    public const SMPTE_24 = 24.0;
    public const SMPTE_25 = 25.0;
    public const SMPTE_30DROP = 29.97;
    public const SMPTE_30 = 30.0;
}
