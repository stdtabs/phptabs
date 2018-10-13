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

use PhpTabs\Music\Note;

class MidiNoteHelper
{
  /**
   * @var \PhpTabs\Writer\Midi\MidiMeasureHelper
   */
  private $measure;

  /**
   * @var \PhpTabs\Music\Note
   */
  private $note;

  /**
   * @param \PhpTabs\Writer\Midi\MidiMeasureHelper $measure
   * @param \PhpTabs\Music\Note $note
   */
  public function __construct(MidiMeasureHelper $measure, Note $note)
  {
    $this->measure = $measure;
    $this->note = $note;
  }

  /**
   * @return \PhpTabs\Writer\Midi\MidiMeasureHelper $measure
   */
  public function getMeasure()
  {
    return $this->measure;
  }

  /**
   * @return \PhpTabs\Music\Note
   */
  public function getNote()
  {
    return $this->note;
  }
}
