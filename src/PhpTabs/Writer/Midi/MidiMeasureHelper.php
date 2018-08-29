<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Midi;

class MidiMeasureHelper
{
  private $index;
  private $move;

  /**
   * @param int $index
   * @param int $move
   */
  public function __construct($index, $move)
  {
    $this->index = $index;
    $this->move = $move;
  }

  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }

  /**
   * @return int
   */
  public function getMove()
  {
    return $this->move;
  }
}
