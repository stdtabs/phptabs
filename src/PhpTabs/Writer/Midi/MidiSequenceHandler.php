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

namespace PhpTabs\Writer\Midi;

use PhpTabs\Share\ChannelRoute;
use PhpTabs\Share\ChannelRouter;
use PhpTabs\Music\Duration;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Reader\Midi\MidiEvent;
use PhpTabs\Reader\Midi\MidiSequence;
use PhpTabs\Reader\Midi\MidiTrack;

class MidiSequenceHandler
{
    private $sequence;
    private $router;
    private $tracks;
    private $writer;

    public function __construct(int $tracks, ChannelRouter $router, MidiWriter $writer)
    {
        $this->router = $router;
        $this->tracks = $tracks;
        $this->writer = $writer;
        $this->init();
    }

    private function init(): void
    {
        $this->sequence = new MidiSequence(MidiSequence::PPQ, Duration::QUARTER_TIME);

        for ($i = 0; $i < $this->getTracks(); $i++) {
            $this->sequence->addTrack(new MidiTrack());
        }
    }

    public function getSequence(): MidiSequence
    {
        return $this->sequence;
    }

    public function getTracks(): int
    {
        return $this->tracks;
    }

    private function resolveChannel(ChannelRoute $channel, bool $bendMode): int
    {
        return $bendMode
            ? $channel->getChannel2()
            : $channel->getChannel1();
    }

    public function addEvent(int $track, MidiEvent $event): void
    {
        if ($track >= 0 && $track < $this->getSequence()->countTracks()) {
            $this->getSequence()->getTrack($track)->add($event);
        }
    }

    public function addNoteOff(int $tick, int $track, int $channelId, int $note, int $velocity, bool $bendMode): void
    {
        $channel = $this->router->getRoute($channelId);

        if ($channel !== null) {
            $this->addEvent($track, new MidiEvent(MidiMessageUtils::noteOff($this->resolveChannel($channel, $bendMode), $note, $velocity), $tick));
        }
    }

    public function addNoteOn(int $tick, int $track, int $channelId, int $note, int $velocity, bool $bendMode): void
    {
        $channel = $this->router->getRoute($channelId);

        if ($channel !== null) {
            $this->addEvent($track, new MidiEvent(MidiMessageUtils::noteOn($this->resolveChannel($channel, $bendMode), $note, $velocity), $tick));
        }
    }

    public function addPitchBend(int $tick, int $track, int $channelId, int $value, bool $bendMode): void
    {
        $channel = $this->router->getRoute($channelId);

        if ($channel !== null) {
            $this->addEvent($track, new MidiEvent(MidiMessageUtils::pitchBend($this->resolveChannel($channel, $bendMode), $value), $tick));
        }
    }

    public function addControlChange(int $tick, int $track, int $channelId, int $controller, int $value): void
    {
        $channel = $this->router->getRoute($channelId);

        if ($channel !== null) {
            $this->addEvent($track, new MidiEvent(MidiMessageUtils::controlChange($channel->getChannel1(), $controller, $value), $tick));

            if ($channel->getChannel1() != $channel->getChannel2()) {
                $this->addEvent($track, new MidiEvent(MidiMessageUtils::controlChange($channel->getChannel2(), $controller, $value), $tick));
            }
        }
    }

    public function addProgramChange(int $tick, int $track, int $channelId, int $instrument): void
    {
        $channel = $this->router->getRoute($channelId);

        if ($channel !== null) {
            $this->addEvent($track, new MidiEvent(MidiMessageUtils::programChange($channel->getChannel1(), $instrument), $tick));

            if ($channel->getChannel1() != $channel->getChannel2()) {
                $this->addEvent($track, new MidiEvent(MidiMessageUtils::programChange($channel->getChannel2(), $instrument), $tick));
            }
        }
    }

    public function addTempoInUSQ(int $tick, int $track, int $usq): void
    {
        $this->addEvent($track, new MidiEvent(MidiMessageUtils::tempoInUSQ($usq), $tick));
    }

    public function addTimeSignature(int $tick, int $track, TimeSignature $timeSignature): void
    {
        $this->addEvent($track, new MidiEvent(MidiMessageUtils::timeSignature($timeSignature), $tick));
    }

    public function notifyFinish(): void
    {
        $this->getSequence()->finish();
        $this->writer->write($this->getSequence(), 1);
    }
}
