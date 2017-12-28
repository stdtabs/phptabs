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

use PhpTabs\Music\EffectBend;
use PhpTabs\Music\EffectTremoloBar;
use PhpTabs\Music\NoteEffect;

class NoteEffectParser extends ParserBase
{
  protected $required = [
        'bend',
        'tremoloBar',
        'harmonic',
        'grace',
        'trill',
        'tremoloPicking',
        'vibrato',
        'deadNote',
        'slide',
        'hammer',
        'ghostNote',
        'accentuatedNote',
        'heavyAccentuatedNote',
        'palmMute',
        'staccato',
        'tapping',
        'slapping',
        'popping',
        'fadeIn',
        'letRing'
  ];

  private $autoset = [
        'vibrato',
        'deadNote',
        'slide',
        'hammer',
        'ghostNote',
        'accentuatedNote',
        'heavyAccentuatedNote',
        'palmMute',
        'staccato',
        'tapping',
        'slapping',
        'popping',
        'fadeIn',
        'letRing'
  ];

  /**
   * Parse a note effect array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $effect = new NoteEffect();

    if ($data['bend'] !== null) {
      $effect->setBend(
        $this->parseEffectPoints($data['bend'], new EffectBend())
      );
    }

    if ($data['tremoloBar'] !== null) {
      $effect->setTremoloBar(
        $this->parseEffectPoints($data['tremoloBar'], new EffectTremoloBar())
      );
    }

    if ($data['harmonic'] !== null) {
      $effect->setHarmonic(
        $this->parseHarmonic($data['harmonic'])
      );
    }

    if ($data['grace'] !== null) {
      $effect->setGrace(
        $this->parseGrace($data['grace'])
      );
    }

    if ($data['trill'] !== null) {
      $effect->setTrill(
        $this->parseTrill($data['trill'])
      );
    }

    if ($data['tremoloPicking'] !== null) {
      $effect->setTremoloPicking(
        $this->parseTremoloPicking($data['tremoloPicking'])
      );
    }

    foreach ($this->autoset as $key) {
      if ($data[$key] !== null) {
        $method = 'set' . ucfirst($key);
        $effect->$method($data[$key]);
      }
    }

    $this->item = $effect;
  }
}
