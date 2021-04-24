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

namespace PhpTabs\Music;

abstract class Velocities
{
    public const MIN_VELOCITY       = 15;
    public const VELOCITY_INCREMENT = 16;
    public const PIANO_PIANISSIMO   = 15;
    public const PIANISSIMO         = 31;
    public const PIANO              = 47;
    public const MEZZO_PIANO        = 63;
    public const MEZZO_FORTE        = 79;
    public const FORTE              = 95;
    public const FORTE_FORTISSIMO   = 127;
    public const _DEFAULT           = 95; // FORTE
}
