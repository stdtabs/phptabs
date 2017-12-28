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

use PhpTabs\Music\Chord;

class ChordParser extends ParserBase
{
  protected $required = ['firstFret', 'name', 'strings'];

  /**
   * Parse a chord array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $chord = new Chord(count($data['strings']));
    $chord->setName($data['name']);
    $chord->setFirstFret($data['firstFret']);

    foreach ($data['strings'] as $index => $string) {
      $this->checkKeys($string, 'string');
      $chord->addFretValue($index, $string['string']);
    }

    $this->item = $chord;
  }
}
