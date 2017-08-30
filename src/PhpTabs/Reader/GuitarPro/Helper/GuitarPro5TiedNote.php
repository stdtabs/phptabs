<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Music\Track;

class GuitarPro5TiedNote extends AbstractReader
{
  /**
   * @param integer $string String on which note has started
   * @param \PhpTabs\Music\Track $track
   *
   * @return integer tied note value
   */
  public function getTiedNoteValue($string, Track $track)
  {
    $measureCount = $track->countMeasures();

    if ($measureCount > 0)
    {
      for ($m = $measureCount - 1; $m >= 0; $m--)
      {
        $measure = $track->getMeasure($m);

        for ($b = $measure->countBeats() - 1; $b >= 0; $b--)
        {
          $beat = $measure->getBeat($b);

          for ($v = 0; $v < $beat->countVoices(); $v++)
          {
            $voice = $beat->getVoice($v);  

            if (!$voice->isEmpty())
            {
              for ($n = 0; $n < $voice->countNotes(); $n++)
              {
                $note = $voice->getNote($n);

                if ($note->getString() == $string)
                {
                  return $note->getValue();
                }
              }
            }
          }
        }
      }
    }

    return -1;
  }
}
