<?php

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
use PhpTabs\Music\ChannelNames;
use PhpTabs\Music\ChannelParameter;
use PhpTabs\Music\Song;
use PhpTabs\Music\Track;

class GuitarProChannel extends AbstractReader
{
    /**
     * Read Channel informations
     */
    public function readChannel(Song $song, Track $track, array $channels): void
    {
        $gChannel1 = $this->reader->readInt() - 1;
        $gChannel2 = $this->reader->readInt() - 1;

        if ($gChannel1 >= 0 && $gChannel1 < count($channels)) {
            $channel = new Channel();
            $gChannel1Param = new ChannelParameter();
            $gChannel2Param = new ChannelParameter();

            $gChannel1Param->setKey("channel-1");
            $gChannel1Param->setValue("$gChannel1");
            $gChannel2Param->setKey("channel-2");
            $gChannel2Param->setValue(
                $gChannel1 != 9
                ? "$gChannel2" : "$gChannel1"
            );

            $channel->copyFrom($channels[$gChannel1]);

            for ($i = 0; $i < $song->countChannels(); $i++) {
                  $channelAux = $song->getChannel($i);

                for ($n = 0; $n < $channelAux->countParameters(); $n++) {
                    $channelParameter = $channelAux->getParameter($n);

                    if ($channelParameter->getKey() == "$gChannel1") {
                        if ("$gChannel1" == $channelParameter->getValue()) {
                            $channel->setId($channelAux->getId());
                        }
                    }
                }
            }

            if ($channel->getId() <= 0) {
                $channel->setId($song->countChannels() + 1);
                $channel->setName($this->createChannelNameFromProgram($song, $channel));
                $channel->addParameter($gChannel1Param);
                $channel->addParameter($gChannel2Param);

                $song->addChannel($channel);
            }

            $track->setChannelId($channel->getId());
        }
    }

    /**
     * Creates a unique channel name with a program
     */
    protected function createChannelNameFromProgram(Song $song, Channel $channel): string
    {
        $names = ChannelNames::$defaultNames;

        if ($channel->getProgram() >= 0 && isset($names[$channel->getProgram()])) {
            return $this->createChannelName($song, $names[$channel->getProgram()]);
        }

        return $this->createDefaultChannelName($song);
    }

    /**
     * Create a default channel name
     */
    protected function createDefaultChannelName(Song $song): string
    {
        return $this->createChannelName($song, 'Unnamed');
    }

    /**
     * Generate a unique channel name
     */
    protected function createChannelName(Song $song, string $prefix): string
    {
        $number = 0;
        $unusedName = null;

        while ($unusedName === null) {
            $number ++;
            $name = $prefix . ' ' . $number;

            if (!$this->findChannelsByName($song, $name)) {
                $unusedName = $name;
            }
        }

        return $unusedName;
    }

    /**
     * Checks if a channel name is already affected
     */
    protected function findChannelsByName(Song $song, string $name): bool
    {
        $channels = $song->getChannels();

        foreach ($channels as $v) {
            if ($v->getName() == $name) {
                return true;
            }
        }

        return false;
    }
}
