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
final class MidiTrackReaderHelper
{
    /**
     * @var int
     */
    private $ticks = 0;

    /**
     * @var int
     */
    private $remainingBytes;

    /**
     * @var int
     */
    private $runningStatusByte;

    public function __construct(int $ticks, int $remainingBytes, int $runningStatusByte)
    {
        $this->ticks = $ticks;
        $this->remainingBytes = $remainingBytes;
        $this->runningStatusByte = $runningStatusByte;
    }

    public function getRemainingBytes(): int
    {
        return $this->remainingBytes;
    }

    public function decrementRemainingBytes(): void
    {
        $this->remainingBytes--;
    }

    public function addTicks(int $ticks): void
    {
        $this->ticks += $ticks;
    }

    public function getTicks(): int
    {
        return $this->ticks;
    }

    public function setRunningStatusByte(int $statusByte): void
    {
        $this->runningStatusByte = $statusByte;
    }

    public function getRunningStatusByte(): int
    {
        return $this->runningStatusByte;
    }
}
