<?php

declare(strict_types = 1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

class EffectHarmonic
{
    const KEY_NATURAL     = "N.H";
    const KEY_ARTIFICIAL  = "A.H";
    const KEY_TAPPED      = "T.H";
    const KEY_PINCH       = "P.H";
    const KEY_SEMI        = "S.H";

    const TYPE_NATURAL    = 1;
    const TYPE_ARTIFICIAL = 2;
    const TYPE_TAPPED     = 3;
    const TYPE_PINCH      = 4;
    const TYPE_SEMI       = 5;
    const MIN_ARTIFICIAL_OFFSET = -24;
    const MAX_ARTIFICIAL_OFFSET = 24;
    const MAX_TAPPED_OFFSET     = 24;

    public static $naturalFrequencies = [
        array(12, 12), //AH12 (+12 frets)
        array(9, 28), //AH9 (+28 frets)
        array(5, 24), //AH5 (+24 frets)
        array(7, 19), //AH7 (+19 frets)
        array(4, 28), //AH4 (+28 frets)
        array(3, 31)  //AH3 (+31 frets)
    ];

    private $type = 0;
    private $data = 0;

    public function getData(): int
    {
        return $this->data;
    }

    public function setData(int $data): void
    {
        $this->data = $data;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function isNatural(): bool
    {
        return $this->type == EffectHarmonic::TYPE_NATURAL;
    }

    public function isArtificial(): bool
    {
        return $this->type == EffectHarmonic::TYPE_ARTIFICIAL;
    }

    public function isTapped(): bool
    {
        return $this->type == EffectHarmonic::TYPE_TAPPED;
    }

    public function isPinch(): bool
    {
        return $this->type == EffectHarmonic::TYPE_PINCH;
    }

    public function isSemi(): bool
    {
        return $this->type == EffectHarmonic::TYPE_SEMI;
    }
}
