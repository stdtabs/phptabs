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

use PhpTabs\Music\Duration;

class DurationParser extends ParserBase
{
  protected $required = ['value', 'dotted', 'doubleDotted', 'divisionType'];

  /**
   * Parse a duration array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);
    $this->checkKeys($data['divisionType'], ['enters', 'times']);

    $duration = new Duration();
    $duration->setValue($data['value']);
    $duration->setDotted($data['dotted']);
    $duration->setDoubleDotted($data['doubleDotted']);
    $duration->getDivision()->setEnters($data['divisionType']['enters']);
    $duration->getDivision()->setTimes($data['divisionType']['times']);

    $this->item = $duration;
  }
}
