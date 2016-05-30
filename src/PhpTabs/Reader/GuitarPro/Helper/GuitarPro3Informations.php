<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Song;

class GuitarPro3Informations extends AbstractReader
{
  /**
   * Reads meta informations about tablature
   * 
   * @param Song $song
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
    for($i = 0; $i < $comments; $i++)
    {
      $song->setComments($song->getComments() . $this->reader->readStringByteSizeOfInteger());
    }
  }
}
