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

final class MidiTickHelper
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $duration;

    public function __construct(int $start, int $duration)
    {
        $this->start = $start;
        $this->duration = $duration;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getStart(): int
    {
        return $this->start;
    }
}
