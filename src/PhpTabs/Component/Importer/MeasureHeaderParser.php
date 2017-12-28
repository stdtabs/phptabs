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

use PhpTabs\Music\MeasureHeader;

class MeasureHeaderParser extends ParserBase
{
  protected $required = [
      'number',
      'start',
      'timeSignature',
      'tempo',
      'repeatOpen',
      'repeatAlternative',
      'repeatClose',
      'tripletFeel'
  ];

  /**
   * Parse a measure header array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $header = new MeasureHeader();
    $header->setNumber($data['number']);
    $header->setStart($data['start']);

    $header->setTimeSignature(
      $this->parseTimeSignature($data['timeSignature'])
    );

    $header->setTempo(
      $this->parseTempo($data['tempo'])
    );

    if (isset($data['marker'])) {
      $header->setMarker(
        $this->parseMarker($data['marker'])
      );
    }

    $header->setRepeatOpen($data['repeatOpen']);
    $header->setRepeatAlternative($data['repeatAlternative']);
    $header->setRepeatClose($data['repeatClose']);
    $header->setTripletFeel($data['tripletFeel']);

    $this->item = $header;
  }
}
