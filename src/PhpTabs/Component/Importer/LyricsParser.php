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

use PhpTabs\Music\Lyric;

class LyricsParser extends ParserBase
{
  protected $required = ['from', 'lyrics'];

  /**
   * Parse a color array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $lyric = new Lyric();
    $lyric->setFrom($data['from']);
    $lyric->setLyrics($data['lyrics']);

    $this->item = $lyric;
  }
}
