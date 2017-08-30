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

use PhpTabs\Music\Song;

class GuitarPro3Informations extends AbstractReader
{
  /**
   * Reads meta informations about tablature
   * 
   * @param \PhpTabs\Music\Song $song
   */
  public function readInformations(Song $song)
  {
    $song->setName($this->reader->readStringByteSizeOfInteger());
    $this->reader->readStringByteSizeOfInteger();
    $song->setArtist($this->reader->readStringByteSizeOfInteger());
    $song->setAlbum($this->reader->readStringByteSizeOfInteger());
    $song->setAuthor($this->reader->readStringByteSizeOfInteger());
    $song->setCopyright($this->reader->readStringByteSizeOfInteger());
    $song->setWriter($this->reader->readStringByteSizeOfInteger());
    $song->setDate($this->reader->readStringByteSizeOfInteger());

    $comments = $this->reader->readInt();

    for ($i = 0; $i < $comments; $i++)
    {
      $song->setComments($song->getComments() . $this->reader->readStringByteSizeOfInteger());
    }
  }
}
