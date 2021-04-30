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

use PhpTabs\Music\Channel;

final class GuitarProChannels extends AbstractReader
{
    /**
     * Read channels informations
     *
     * @return array<Channel>
     */
    public function readChannels(): array
    {
        $channels = [];

        for ($i = 0; $i < 64; $i++) {
            $channel = new Channel();
            $channel->setProgram($this->reader->readInt());
            $channel->setVolume($this->toChannelShort($this->reader->readByte()));
            $channel->setBalance($this->toChannelShort($this->reader->readByte()));
            $channel->setChorus($this->toChannelShort($this->reader->readByte()));
            $channel->setReverb($this->toChannelShort($this->reader->readByte()));
            $channel->setPhaser($this->toChannelShort($this->reader->readByte()));
            $channel->setTremolo($this->toChannelShort($this->reader->readByte()));
            $channel->setBank(
                $i === 9
                    ? Channel::DEFAULT_PERCUSSION_BANK
                    : Channel::DEFAULT_BANK
            );

            if ($channel->getProgram() < 0) {
                $channel->setProgram(0);
            }

            $channels[] = $channel;

            $this->reader->skip(2);
        }

        return $channels;
    }

    /**
     * Formats an integer between 0 and 32767 from bytes
     */
    protected function toChannelShort(int $bytes): int
    {
        $value = ($bytes * 8) - 1;

        return max($value, 0);
    }
}
