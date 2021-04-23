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

final class Scale
{
    private $notes = []; // 12
    private $key;

    public function __construct()
    {
        $this->clear();
    }

    public function setKey(int $key): void
    {
        $this->key = $key;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function setNote(int $note, bool $on): void
    {
        $this->notes[$note] = $on;
    }

    public function getNote(int $note): int
    {
        return $this->notes[(($note + (12 - $this->key)) % 12)];
    }

    public function clear(): void
    {
        $this->setKey(0);

        $count = count($this->notes);
        for ($i = 0; $i < $count; $i++) {
            $this->setNote($i, false);
        }
    }
}
