<?php

namespace PhpTabs\Music;

class Lyric
{
  const REGEX = " ";

  private $from;
  private $lyrics = array();

  public function __construct()
  {
    $this->from = 1;
    $this->lyrics = array();
  }

  /**
   * @return int
   */
  public function getFrom()
  {
    return $this->from;
  }

  /**
   * @param int $from
   */
  public function setFrom($from)
  {
    $this->from = $from;
  }

  /**
   * @return array
   */
  public function getLyrics()
  {
    return $this->lyrics;
  }

  /**
   * @param array $lyrics
   */
  public function setLyrics($lyrics)
  {
    $this->lyrics = $lyrics;
  }

  /**
   * @return array
   */
  public function getLyricBeats()
  {
    $lyrics = $this->getLyrics();

    $str = '';

    foreach ($lyrics as $value)
    {
      $str .= str_replace(array("\n", "\r"), Lyric::REGEX, $value) . Lyric::REGEX; 
    }

    return explode(Lyric::REGEX, $str);
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
    return count($this->getLyrics()) == 0;
  }

  /**
   * @param \PhpTabs\Music\Lyric $lyric
   */
  public function copyFrom(Lyric $lyric)
  {
    $this->setFrom($lyric->getFrom());
    $this->setLyrics($lyric->getLyrics());
  }

  /**
   * @return \PhpTabs\Music\Lyric
   */
  public function __clone()
  {
    $lyric = new Lyric();
    $lyric->copyFrom($this);
    return $lyric;
  }
}
