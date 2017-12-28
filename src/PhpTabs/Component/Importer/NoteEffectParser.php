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

  private $parsers = [
    'harmonic'        => 'Harmonic',
    'grace'           => 'Grace',
    'trill'           => 'Trill',
    'tremoloPicking'  => 'TremoloPicking'
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

    $this->item = new NoteEffect();

    $this->createBendAndTremolo($data);

    $this->createAttributes($data);
  }

  /**
   * Create bend and tremolo bar values
   *
   * @param array $data
   */
  private function createBendAndTremolo(array $data)
  {
    if ($data['bend'] !== null) {
      $this->item->setBend(
        $this->parseEffectPoints($data['bend'], new EffectBend())
      );
    }

    if ($data['tremoloBar'] !== null) {
      $this->item->setTremoloBar(
        $this->parseEffectPoints($data['tremoloBar'], new EffectTremoloBar())
      );
    }
  }

  /**
   * Create attributes
   *
   * @param array $data
   */
  private function createAttributes(array $data)
  {
    foreach ($this->parsers as $name => $parser) {
      if ($data[$name] !== null) {
        $setter = 'set' . $parser;
        $getter = 'parse' . $parser;
        
        $this->item->$setter(
          $this->$getter($data[$name])
        );
      }
    }

    foreach ($this->autoset as $key) {
      if ($data[$key] !== null) {
        $method = 'set' . ucfirst($key);
        $this->item->$method($data[$key]);
      }
    }
  }
}
