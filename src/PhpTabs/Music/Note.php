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

final class Note
{
    /**
     * @var int
     */
    private $value = 0;

    /**
     * @var int
     */
    private $string = 1;

    /**
     * @var bool
     */
    private $tiedNote = false;

    /**
     * @var int
     */
    private $velocity = Velocities::FORTE;

    /**
     * @var NoteEffect
     */
    private $effect;

    /**
     * @var Voice
     */
    private $voice;

    public function __construct()
    {
        $this->effect = new NoteEffect();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getVelocity(): int
    {
        return $this->velocity;
    }

    public function setVelocity(int $velocity): void
    {
        $this->velocity = $velocity;
    }

    public function getString(): int
    {
        return $this->string;
    }

    public function setString(int $string): void
    {
        $this->string = $string;
    }

    public function isTiedNote(): bool
    {
        return $this->tiedNote;
    }

    public function setTiedNote(bool $tiedNote): void
    {
        $this->tiedNote = $tiedNote;
    }

    public function getEffect(): NoteEffect
    {
        return $this->effect;
    }

    public function setEffect(NoteEffect $effect): void
    {
        $this->effect = $effect;
    }

    public function getVoice(): Voice
    {
        return $this->voice;
    }

    public function setVoice(Voice $voice): void
    {
        $this->voice = $voice;
    }

    public function __clone()
    {
        $this->effect = clone $this->effect;
    }
}
