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
use PhpTabs\Music\Duration;
use PhpTabs\Music\Stroke;

class GuitarPro3Stroke extends AbstractReader
{
    public function readStroke(Beat $beat): void
    {
        $strokeDown = $this->reader->readByte();
        $strokeUp = $this->reader->readByte();

        if ($strokeDown > 0 ) {
            $beat->getStroke()->setDirection(Stroke::STROKE_DOWN);
            $beat->getStroke()->setValue($this->toStrokeValue($strokeDown));
        } elseif ($strokeUp > 0) {
            $beat->getStroke()->setDirection(Stroke::STROKE_UP);
            $beat->getStroke()->setValue($this->toStrokeValue($strokeUp));
        }
    }

    /**
     * Get stroke value
     *
     * @return int stroke value
     */
    public function toStrokeValue(int $value): int
    {
        if ($value == 1 || $value == 2) {
            return Duration::SIXTY_FOURTH;
        }

        if ($value == 3) {
            return Duration::THIRTY_SECOND;
        }

        if ($value == 4) {
            return Duration::SIXTEENTH;
        }

        if ($value == 5) {
            return Duration::EIGHTH;
        }

        if ($value == 6) {
            return Duration::QUARTER;
        }

        return Duration::SIXTY_FOURTH;
    }
}
