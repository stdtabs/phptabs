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

final class Text
{
    private $value;
    private $beat;

    public function getBeat(): ?Beat
    {
        return $this->beat;
    }

    public function setBeat(Beat $beat): void
    {
        $this->beat = $beat;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function isEmpty(): bool
    {
        return is_null($this->value)
            || strlen($this->value) === 0;
    }

    public function copyFrom(Text $text): void
    {
        $this->setValue($text->getValue());
    }
}
