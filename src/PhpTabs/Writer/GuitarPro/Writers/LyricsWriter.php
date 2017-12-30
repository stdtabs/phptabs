<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro\Writers;

use PhpTabs\Music\Song;

class LyricsWriter
{
  private $writer;

  public function __construct($writer)
  {
    $this->writer = $writer;
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  public function writeLyrics(Song $song)
  {
    $lyricTrack = null;
    $tracks = $song->getTracks();

    foreach ($tracks as $track) {
      if (!$track->getLyrics()->isEmpty()) {
        $lyricTrack = $track;
        break;
      }
    }

    $this->writer->writeInt($lyricTrack == null ? 0 : $lyricTrack->getNumber());
    $this->writer->writeInt($lyricTrack == null ? 0 : $lyricTrack->getLyrics()->getFrom());
    $this->writer->writeStringInteger(
      $lyricTrack == null ? '' : $lyricTrack->getLyrics()->getLyrics()
    );

    for ($i = 0; $i < 4; $i++) {
      $this->writer->writeInt($lyricTrack === null ? 0 : 1);
      $this->writer->writeStringInteger('');
    }
  }
}
