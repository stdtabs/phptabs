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

use PhpTabs\Music\Beat;

class BeatParser extends ParserBase
{
  protected $required = ['start', 'voices', 'stroke'];

  /**
   * Parse a beat array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $beat = new Beat();
    $beat->setStart($data['start']);
    
    if (isset($data['chord'])) {
      $beat->setChord(
        $this->parseChord($data['chord'])
      );
    }

    if (isset($data['text'])) {
      $beat->setText(
        $this->parseText($data['text'])
      );
    }

    $this->checkKeys($data['stroke'], ['direction', 'value']);
    $beat->getStroke()->setDirection($data['stroke']['direction']);
    $beat->getStroke()->setValue($data['stroke']['value']);

    foreach ($data['voices'] as $index => $voice) {
      $this->checkKeys($voice, 'voice');
      $beat->setVoice(
        $index,
        $this->parseVoice($index, $voice['voice'])
      );
    }

    $this->item = $beat;
  }
}
