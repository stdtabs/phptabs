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

use PhpTabs\Music\TabString;

class StringParser extends ParserBase
{
  protected $required = ['number', 'value'];

  /**
   * Parse a string array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $this->item = new TabString($data['number'], $data['value']);
  }
}
