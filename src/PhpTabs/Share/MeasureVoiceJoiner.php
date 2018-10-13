<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Share;

use PhpTabs\Music\Duration;
use PhpTabs\Music\Measure;

class MeasureVoiceJoiner
{
  private $measure;

  /**
   * @param \PhpTabs\Music\Measure $measure
   */
  public function __construct(Measure $measure)
  {
    $this->measure = clone $measure;
    $this->measure->setTrack($measure->getTrack());
  }

  /**
   * @return \PhpTabs\Music\Measure
   */
  public function process()
  {
    $this->orderBeats();
    $this->joinBeats();

    return $this->measure;
  }

  public function joinBeats()
  {
    $previous = null;
    $finish = true;

    $measureStart = $this->measure->getStart();
    $measureEnd = $measureStart + $this->measure->getLength();

    for ($i = 0; $i < $this->measure->countBeats(); $i++) {
      $beat = $this->measure->getBeat($i);
      $voice = $beat->getVoice(0);

      for ($v = 1; $v < $beat->countVoices(); $v++) {
        $currentVoice = $beat->getVoice($v);

        if (!$currentVoice->isEmpty()) {
          for ($n = 0; $n < $currentVoice->countNotes(); $n++) {
            $note = $currentVoice->getNote($n);
            $voice->addNote($note);
          }
        }
      }

      if ($voice->isEmpty()) {
        $this->measure->removeBeat($beat);
        $finish = false;
        break;
      }

      $beatStart = $beat->getStart();

      if ($previous !== null) {
        $previousStart = $previous->getStart();

        $previousBestDuration = null;
        for ($v = 0; $v < $previous->countVoices(); $v++) {
          $previousVoice = $previous->getVoice($v);

          if (!$previousVoice->isEmpty()) {
            $length = $previousVoice->getDuration()->getTime();

            if ($previousStart + $length <= $beatStart) {
              if ($previousBestDuration === null || $length > $previousBestDuration->getTime()) {
                $previousBestDuration = $previousVoice->getDuration();
              }
            }
          }
        }

        if ($previousBestDuration !== null) {
          $previous->getVoice(0)->getDuration()->copyFrom($previousBestDuration);
        } else {
          if ($voice->isRestVoice()) {
            $this->measure->removeBeat($beat);
            $finish = false;
            break;
          }
          $duration = Duration::fromTime($beatStart - $previousStart);
          $previous->getVoice(0)->getDuration()->copyFrom($duration);
        }
      }

      $beatBestDuration = null;
      for ($v = 0; $v < $beat->countVoices(); $v++) {
        $currentVoice = $beat->getVoice($v);

        if (!$currentVoice->isEmpty()) {
          $length = $currentVoice->getDuration()->getTime();

          if ($beatStart + $length <= $measureEnd) {
            if ($beatBestDuration === null || $length > $beatBestDuration->getTime()) {
              $beatBestDuration = $currentVoice->getDuration();
            }
          }
        }
      }

      if ($beatBestDuration === null) {
        if ($voice->isRestVoice()) {
          $this->measure->removeBeat($beat);
          $finish = false;
          break;
        }
        $duration = Duration::fromTime($measureEnd - $beatStart);
        $voice->getDuration()->copyFrom($duration);
      }
      $previous = $beat;
    }

    if (!$finish) {
      $this->joinBeats();
    }
  }

  public function orderBeats()
  {
    for ($i = 0; $i < $this->measure->countBeats(); $i++) {
      $minBeat = null;

      for ($j = $i; $j < $this->measure->countBeats(); $j++) {
        $beat = $this->measure->getBeat($j);

        if ($minBeat === null || $beat->getStart() < $minBeat->getStart()) {
          $minBeat = $beat;
        }
      }

      $this->measure->moveBeat($i, $minBeat);
    }
  }
}
