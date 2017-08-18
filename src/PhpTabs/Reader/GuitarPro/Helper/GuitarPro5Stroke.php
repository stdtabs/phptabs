<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Beat;
use PhpTabs\Music\Stroke;

class GuitarPro5Stroke extends AbstractReader
{
  /**
   * @param \PhpTabs\Music\Beat $beat
   */
  public function readStroke(Beat $beat)
  {
    $strokeUp = $this->reader->readByte();
    $strokeDown = $this->reader->readByte();

    if ($strokeUp > 0)
    {
      $beat->getStroke()->setDirection(Stroke::STROKE_UP);
      $beat->getStroke()->setValue($this->reader->factory('GuitarPro3Stroke')->toStrokeValue($strokeUp));
    }
    elseif ($strokeDown > 0 )
    {
      $beat->getStroke()->setDirection(Stroke::STROKE_DOWN);
      $beat->getStroke()->setValue($this->reader->factory('GuitarPro3Stroke')->toStrokeValue($strokeDown));
    }
  }
}
