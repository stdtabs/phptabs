<?php

namespace PhpTabs\Model;

/**
 * @package Lyric
 */

class Lyric
{
  const REGEX = " ";

  private $from;
  private $lyrics;

  public function __construct()
  {
    $this->from = 1;
    $this->lyrics = array();
  }

  public function getFrom()
  {
    return $this->from;
  }

  public function setFrom($from)
  {
    $this->from = $from;
  }

  public function getLyrics()
  {
    return $this->lyrics;
  }

  public function setLyrics($lyrics)
  {
    $this->lyrics = $lyrics;
  }

  public function getLyricBeats()
  {
    $lyrics = $this->getLyrics();

    $str = '';

    foreach($lyrics as $k=>$v)
      $str .= str_replace(array("\n", "\r"), Lyric::REGEX, $v) . Lyric::REGEX; 

    return explode(Lyric::REGEX, $str);
  }

  public function isEmpty()
  {
    return count($this->getLyrics()) == 0;
  }

  public function copyFrom(Lyric $lyric)
  {
    $this->setFrom($lyric->getFrom());
    $this->setLyrics($lyric->getLyrics());
  }

  public function __clone()
  {
    $lyric = new Lyric();
    $lyric->copyFrom($this);
    return $lyric;
  }
}
