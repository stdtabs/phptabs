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

final class EffectBendPoint extends EffectPointsBase
{
    public function getTime(int $duration): int
    {
        return intval(
            $duration * $this->getPosition()
            / EffectBend::MAX_POSITION_LENGTH
        );
    }
}
