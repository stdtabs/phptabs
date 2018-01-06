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

/**
 * @uses Beat
 * @uses Duration
 */
class Stroke
{
  const STROKE_NONE = 0;
  const STROKE_UP   = 1;
  const STROKE_DOWN = -1;

  private $direction;
  private $value;

  public function __construct()
  {
    $this->direction = Stroke::STROKE_NONE;
  }

  /**
   * @return int
   */
  public function getDirection()
  {
    return $this->direction;
  }

  /**
   * @param int $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }

  /**
   * @return int $value
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
   * 
   * @return int
   */
  public function getIncrementTime(Beat $beat)
  {
    if ($this->value <= 0) {
      return 0;
    }

    $duration = 0;

    foreach ($beat->getVoices() as $voice) {

      if (!$voice->isEmpty()) {
        $currentDuration = $voice->getDuration()->getTime();

        if ($duration == 0 || $currentDuration < $duration) {
          $duration = $currentDuration <= Duration::QUARTER_TIME
                    ? $currentDuration : Duration::QUARTER_TIME;
        }
      }
    }

    return $duration > 0
      ? round(($duration / 8.0) * (4.0 / $this->value))
      : 0;
  }

  /**
   * @return \PhpTabs\Music\Stroke
   */
  public function __clone()
  {
    $stroke = new Stroke();
    $stroke->copyFrom($this);
    return $stroke;
  }

  /**
   * @param \PhpTabs\Music\Stroke $stroke
   */
  public function copyFrom(Stroke $stroke)
  {
    $this->setValue($stroke->getValue());
    $this->setDirection($stroke->getDirection());
  }
}
