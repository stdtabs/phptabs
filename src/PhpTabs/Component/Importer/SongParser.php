<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use PhpTabs\Music\Song;

class SongParser extends ParserBase
{
  protected $required = [
        'name',
        'artist',
        'album',
        'author',
        'copyright',
        'writer',
        'comments',
        'channels',
        'measureHeaders',
        'tracks'
  ];

  /**
   * Parse a song array
   * 
   * @param  array $data
   */
  public function __construct(array $data, Song $song)
  {
    $this->checkKeys($data, $this->required);

    $song->setName($data['name']);
    $song->setArtist($data['artist']);
    $song->setAlbum($data['album']);
    $song->setAuthor($data['author']);
    $song->setCopyright($data['copyright']);
    $song->setWriter($data['writer']);
    $song->setComments($data['comments']);

    $channelCount = count($data['channels']);

    foreach ($data['channels'] as $channel) {
      $this->checkKeys($channel, 'channel');
      $song->addChannel(
        $this->parseChannel($channel['channel'])
      );
    }

    foreach ($data['measureHeaders'] as $header) {
      $this->checkKeys($header, 'header');
      $song->addMeasureHeader(
        $this->parseMeasureHeader($header['header'])
      );
    }

    foreach ($data['tracks'] as $track) {
      $this->checkKeys($track, 'track');
      $song->addTrack(
        $this->parseTrack($track['track'], $song)
      );
    }

    $this->item = $song;
  }
}
