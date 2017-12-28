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

use PhpTabs\Music\EffectGrace;

class GraceParser extends ParserBase
{
  protected $required = [
      'fret',
      'duration',
      'dynamic',
      'transition',
      'onBeat',
      'dead'
  ];

  /**
   * Parse a grace effect array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $grace = new EffectGrace();
    $grace->setFret($data['fret']);
    $grace->setDuration($data['duration']);
    $grace->setDynamic($data['dynamic']);
    $grace->setTransition($data['transition']);
    $grace->setOnBeat($data['onBeat']);
    $grace->setDead($data['dead']);

    $this->item = $grace;
  }
}
