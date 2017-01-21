<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Text;

class GuitarProText extends AbstractReader
{
  /**
   * Reads some text
   * 
   * @param \PhpTabs\Model\Beat $beat
   */
  public function readText(Beat $beat)
  {
    $text = new Text();

    $text->setValue($this->reader->readStringByteSizeOfInteger());

    $beat->setText($text);
  }
}
