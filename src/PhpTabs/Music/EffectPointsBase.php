<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

class EffectPointsBase
{
  protected $position;
  protected $value;

  /**
   * @param int $position
   * @param int $value
   */
  public function __construct($position, $value)
  {
    $this->position = $position;
    $this->value = $value;
  }

  /**
   * @return int $position
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @return int $value
   */
  public function getValue()
  {
    return $this->value;
  }
}
