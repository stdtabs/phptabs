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

use PhpTabs\Music\ChannelParameter;

class ChannelParameterParser extends ParserBase
{
  protected $required = ['key', 'value'];

  /**
   * Parse a channel parameter array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $parameter = new ChannelParameter();
    $parameter->setKey($data['key']);
    $parameter->setValue($data['value']);

    $this->item = $parameter;
  }
}
