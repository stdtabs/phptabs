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

class MidiTickHelper
{
  private $start;
  private $duration;

  /**
   * @param int $start
   * @param int $duration
   */
  public function __construct($start, $duration)
  {
    $this->start = $start;
    $this->duration = $duration;
  }

  /**
   * @return int
   */
  public function getDuration()
  {
    return $this->duration;
  }

  /**
   * @return int
   */
  public function getStart()
  {
    return $this->start;
  }
}
