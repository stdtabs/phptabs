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

class MidiSequenceHelper
{
  private $measureHeaderHelpers;
  private $sequence;

  /**
   * @param \PhpTabs\Writer\Midi\MidiSequenceHandler $sequence
   */
  public function __construct(MidiSequenceHandler $sequence)
  {
    $this->sequence = $sequence;
    $this->measureHeaderHelpers = array();
  }

  /**
   * @return \PhpTabs\Writer\Midi\MidiSequenceHandler
   */
  public function getSequence()
  {
    return $this->sequence;
  }

  /**
   * @param \PhpTabs\Writer\Midi\MidiMeasureHelper $helper
   */
  public function addMeasureHelper(MidiMeasureHelper $helper)
  {
    $this->measureHeaderHelpers[] = $helper;
  }

  /**
   * @return array
   */
  public function getMeasureHelpers()
  {
    return $this->measureHeaderHelpers;
  }

  /**
   * @return \PhpTabs\Writer\Midi\MidiMeasureHelper $helper
   */
  public function getMeasureHelper($index)
  {
    return $this->measureHeaderHelpers[$index];
  }
}
