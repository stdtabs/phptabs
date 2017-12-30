<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro;

use Exception;
use PhpTabs\Music\Chord;
use PhpTabs\Music\Color;
use PhpTabs\Music\Duration;
use PhpTabs\Music\Marker;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;
use PhpTabs\Music\Stroke;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Text;
use PhpTabs\Music\Track;

class GuitarPro4Writer extends GuitarProWriterBase
{
  /**
   * @constant version
   */
  const VERSION = 'FICHIER GUITAR PRO v4.00';

  /**
   * @param \PhpTabs\Music\Song $song
   */
  public function __construct(Song $song)
  {
    parent::__construct();

    if ($song->isEmpty()) {
      throw new Exception('Song is empty');
    }

    $this->configureChannelRouter($song);
    $header = $song->getMeasureHeader(0);
    $this->writeStringByte(self::VERSION, 30);
    $this->writeInformations($song);
    $this->writeBoolean(
      $header->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_EIGHTH
    );
    $this->getWriter('LyricsWriter')->writeLyrics($song);
    $this->writeInt($header->getTempo()->getValue());
    $this->writeInt(0);
    $this->writeByte(0);
    $this->getWriter('ChannelWriter')->writeChannels($song);
    $this->writeInt($song->countMeasureHeaders());
    $this->writeInt($song->countTracks());
    $this->getWriter('MeasureHeaderWriter')->writeMeasureHeaders($song);
    $this->getWriter('TrackWriter')->writeTracks($song);
    $this->getWriter('MeasureWriter')->writeMeasures($song, clone $header->getTempo());   
  }

  /**
   * @param \PhpTabs\Music\Chord $chord
   */
  public function writeChord(Chord $chord)
  {
    $this->writeUnsignedByte(0x01);
    $this->skipBytes(16);
    $this->writeStringByte($chord->getName(), 21);
    $this->skipBytes(4);
    $this->writeInt($chord->getFirstFret());

    for ($i = 0; $i < 7; $i++) {
      $this->writeInt($i < $chord->countStrings() ? $chord->getFretValue($i) : -1);
    }

    $this->skipBytes(32);
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeInformations(Song $song)
  {
    $this->writeStringByteSizeOfInteger($song->getName());
    $this->writeStringByteSizeOfInteger("");
    $this->writeStringByteSizeOfInteger($song->getArtist());
    $this->writeStringByteSizeOfInteger($song->getAlbum());
    $this->writeStringByteSizeOfInteger($song->getAuthor());
    $this->writeStringByteSizeOfInteger($song->getCopyright());
    $this->writeStringByteSizeOfInteger($song->getWriter());
    $this->writeStringByteSizeOfInteger("");

    $comments = $this->toCommentLines($song->getComments());
    $this->writeInt(count($comments));

    for ($i = 0; $i < count($comments); $i++) {
      $this->writeStringByteSizeOfInteger($comments[$i]);
    }
  }

  /**
   * @param \PhpTabs\Music\Marker $marker
   */
  public function writeMarker(Marker $marker)
  {
    $this->writeStringByteSizeOfInteger($marker->getTitle());
    $this->writeColor($marker->getColor());
  }

  /**
   * @param \PhpTabs\Music\Tempo $tempo
   */
  public function writeMixChange(Tempo $tempo)
  {
    for ($i = 0; $i < 7; $i++) {
      $this->writeByte(-1);
    }

    $this->writeInt($tempo->getValue());
    $this->writeByte(0);
    $this->writeUnsignedByte(1);
  }

  /**
   * @param  string $comments
   * @return array
   */
  private function toCommentLines($comments)
  {
    $lines = array();
    $line  = $comments;

    while (strlen($line) > 127) {
      $lines[] = substr($line, 0, 127);
      $line    = substr($line, 127);
    }

    $lines[] = $line;

    return $lines;
  }

  /**
   * @param \PhpTabs\Music\Text $text
   */
  public function writeText(Text $text)
  {
    $this->writeStringByteSizeOfInteger($text->getValue());
  }
}
