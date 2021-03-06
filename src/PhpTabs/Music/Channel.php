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

final class Channel
{
    public const DEFAULT_PERCUSSION_CHANNEL = 9;
    public const DEFAULT_PERCUSSION_PROGRAM = 0;
    public const DEFAULT_PERCUSSION_BANK = 128;

    public const DEFAULT_BANK = 0;
    public const DEFAULT_PROGRAM = 25;
    public const DEFAULT_VOLUME = 127;
    public const DEFAULT_BALANCE = 64;
    public const DEFAULT_CHORUS = 0;
    public const DEFAULT_REVERB = 0;
    public const DEFAULT_PHASER = 0;
    public const DEFAULT_TREMOLO = 0;

    /**
     * @var int
     */
    private $id  = 0;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var int
     */
    private $parameters = [];

    /**
     * @var int
     */
    private $bank = Channel::DEFAULT_BANK;

    /**
     * @var int
     */
    private $program = Channel::DEFAULT_PROGRAM;

    /**
     * @var int
     */
    private $volume = Channel::DEFAULT_VOLUME;

    /**
     * @var int
     */
    private $balance = Channel::DEFAULT_BALANCE;

    /**
     * @var int
     */
    private $chorus = Channel::DEFAULT_CHORUS;

    /**
     * @var int
     */
    private $reverb = Channel::DEFAULT_REVERB;

    /**
     * @var int
     */
    private $phaser = Channel::DEFAULT_PHASER;

    /**
     * @var int
     */
    private $tremolo = Channel::DEFAULT_TREMOLO;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }

    public function getChorus(): int
    {
        return $this->chorus;
    }

    public function setChorus(int $chorus): void
    {
        $this->chorus = $chorus;
    }

    public function getBank(): int
    {
        return $this->bank;
    }

    public function setBank(int $bank): void
    {
        $this->bank = $bank;
    }

    public function getProgram(): int
    {
        return $this->program;
    }

    public function setProgram(int $program): void
    {
        $this->program = $program;
    }

    public function getPhaser(): int
    {
        return $this->phaser;
    }

    public function setPhaser(int $phaser): void
    {
        $this->phaser = $phaser;
    }

    public function getReverb(): int
    {
        return $this->reverb;
    }

    public function setReverb(int $reverb): void
    {
        $this->reverb = $reverb;
    }

    public function getTremolo(): int
    {
        return $this->tremolo;
    }

    public function setTremolo(int $tremolo): void
    {
        $this->tremolo = $tremolo;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): void
    {
        $this->volume = $volume;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<ChannelParameter>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function addParameter(ChannelParameter $parameter): void
    {
        $this->parameters[] = $parameter;
    }

    public function setParameter(int $index, ChannelParameter $parameter): void
    {
        $this->parameters[$index] = $parameter;
    }

    public function getParameter(int $index): ?ChannelParameter
    {
        return $this->parameters[$index] ?? null;
    }

    public function removeParameter(int $index): void
    {
        array_splice($this->parameters, $index, 1);
    }

    public function countParameters(): int
    {
        return count($this->parameters);
    }

    public function isPercussionChannel(): bool
    {
        return $this->getBank() === Channel::DEFAULT_PERCUSSION_BANK;
    }

    public function __clone()
    {
        foreach ($this->parameters as $index => $parameter) {
            $this->parameters[$index] = clone $parameter;
        }
    }

    public function copyFrom(Channel $channel): void
    {
        $this->setId($channel->getId());
        $this->setBank($channel->getBank());
        $this->setProgram($channel->getProgram());
        $this->setVolume($channel->getVolume());
        $this->setBalance($channel->getBalance());
        $this->setChorus($channel->getChorus());
        $this->setReverb($channel->getReverb());
        $this->setPhaser($channel->getPhaser());
        $this->setTremolo($channel->getTremolo());
        $this->setName($channel->getName());

        $this->parameters = [];

        $count = $channel->countParameters();
        for ($i = 0; $i < $count; $i++) {
            $this->addParameter(clone $channel->getParameter($i));
        }
    }
}
