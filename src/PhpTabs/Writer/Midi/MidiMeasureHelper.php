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

final class MidiMeasureHelper
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var int
     */
    private $move;

    public function __construct(int $index, int $move)
    {
        $this->index = $index;
        $this->move = $move;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getMove(): int
    {
        return $this->move;
    }
}
