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

use PhpTabs\Music\Color;

class ColorParser extends ParserBase
{
  protected $required = ['R', 'G', 'B'];

  /**
   * Parse a color array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $color = new Color();
    $color->setR($data['R']);
    $color->setG($data['G']);
    $color->setB($data['B']);

    $this->item = $color;
  }
}
