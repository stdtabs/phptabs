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

use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;

class MeasureParser extends ParserBase
{
  protected $required = ['clef', 'keySignature', 'beats'];

  /**
   * Parse a color array
   * 
   * @param  array $data
   * @param  \PhpTabs\Music\MeasureHeader $header
   */
  public function __construct(array $data, MeasureHeader $header)
  {
    $this->checkKeys($data, $this->required);

    $measure = new Measure($header);
    $measure->setClef($data['clef']);
    $measure->setKeySignature($data['keySignature']);

    foreach ($data['beats'] as $beat) {
      $this->checkKeys($beat, 'beat');
      $measure->addBeat(
        $this->parseBeat($beat['beat'])
      );
    }

    $this->item = $measure;
  }
}
