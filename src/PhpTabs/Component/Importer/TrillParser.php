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

use PhpTabs\Music\EffectTrill;

class TrillParser extends ParserBase
{
  protected $required = ['fret', 'duration'];

  /**
   * Parse a trill effect array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $effect = new EffectTrill();
    $effect->setFret($data['fret']);
    $effect->setDuration(
      $this->parseDuration($data['duration'])
    );

    $this->item = $effect;
  }
}
