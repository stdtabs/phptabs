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

abstract class SongBase
{
    /**
     * @var ?string
     */
    protected $name;

    /**
     * @var ?string
     */
    protected $artist;

    /**
     * @var ?string
     */
    protected $album;

    /**
     * @var ?string
     */
    protected $author;

    /**
     * @var ?string
     */
    protected $date;

    /**
     * @var ?string
     */
    protected $copyright;

    /**
     * @var ?string
     */
    protected $writer;

    /**
     * @var ?string
     */
    protected $transcriber;

    /**
     * @var ?string
     */
    protected $comments;

    /**
     * @var array<Track>
     */
    protected $tracks = [];

    /**
     * @var array<MeasureHeader>
     */
    protected $measureHeaders = [];

    /**
     * @var array<Channel>
     */
    protected $channels = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(?string $album): void
    {
        $this->album = $album;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(?string $artist): void
    {
        $this->artist = $artist;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): void
    {
        $this->copyright = $copyright;
    }

    public function getWriter(): ?string
    {
        return $this->writer;
    }

    public function setWriter(?string $writer): void
    {
        $this->writer = $writer;
    }

    public function getTranscriber(): ?string
    {
        return $this->transcriber;
    }

    public function setTranscriber(?string $transcriber): void
    {
        $this->transcriber = $transcriber;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    public function countChannels(): int
    {
        return count($this->channels);
    }

    public function countTracks(): int
    {
        return count($this->tracks);
    }

    public function countMeasureHeaders(): int
    {
        return count($this->measureHeaders);
    }

    public function clear(): void
    {
        $tracks = $this->getTracks();

        foreach ($tracks as $track) {
            $track->clear();
        }

        $this->tracks = [];
        $this->channels = [];
        $this->measureHeaders = [];
    }

    public function isEmpty(): bool
    {
        return $this->countMeasureHeaders() === 0
            || $this->countTracks() === 0;
    }

    public function copyFrom(Song $song): void
    {
        $this->clear();
        $this->setName($song->getName());
        $this->setArtist($song->getArtist());
        $this->setAlbum($song->getAlbum());
        $this->setAuthor($song->getAuthor());
        $this->setDate($song->getDate());
        $this->setCopyright($song->getCopyright());
        $this->setWriter($song->getWriter());
        $this->setTranscriber($song->getTranscriber());
        $this->setComments($song->getComments());

        $headers = $song->getMeasureHeaders();
        foreach ($headers as $header) {
            $this->addMeasureHeader(clone $header);
        }

        $channels = $song->getChannels();
        foreach ($channels as $channel) {
            $this->addChannel(clone $channel);
        }

        $tracks = $song->getTracks();
        foreach ($tracks as $track) {
            $this->addTrack(clone $track);
        }
    }

    abstract public function getTracks(): array;
    abstract public function addMeasureHeader(MeasureHeader $measureHeader): void;
    abstract public function addChannel($index, ?Channel $channel = null): void;
    abstract public function addTrack(Track $track): void;
}
