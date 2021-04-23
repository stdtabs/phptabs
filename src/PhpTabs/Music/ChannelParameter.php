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

final class ChannelParameter
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var int|string|null
     */
    private $value;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return int|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int|string|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function copyFrom(ChannelParameter $channelParameter): void
    {
        $this->setKey($channelParameter->getKey());
        $this->setValue($channelParameter->getValue());
    }
}
