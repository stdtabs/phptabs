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

use PhpTabs\Music\EffectHarmonic;

class HarmonicParser extends ParserBase
{
  protected $required = [
      'type',
      'data',
      'isNatural',
      'isArtificial',
      'isTapped',
      'isPinch',
      'isSemi'
  ];

  /**
   * Parse an harmonic array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $harmonic = new EffectHarmonic();
    $harmonic->setType($data['type']);
    $harmonic->setData($data['data']);

    $this->item = $harmonic;
  }
}
