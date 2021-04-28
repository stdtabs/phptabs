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

namespace PhpTabs\Reader\GuitarPro;

use PhpTabs\Component\ReaderInterface;

/**
 * Interface for Guitar Pro Readers
 */
interface GuitarProReaderInterface extends ReaderInterface
{
    public const GP_BEND_SEMITONE = 25;
    public const GP_BEND_POSITION = 60;

    /**
     * An array of supported versions
     */
    public function getSupportedVersions(): array;
}
