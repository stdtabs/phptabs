<?php

namespace PhpTabs\Model;

/**
 * @uses Beat
 * @uses Duration
 */
class Stroke
{
  const STROKE_NONE = 0;
  const STROKE_UP = 1;
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
   * @param \PhpTabs\Model\Beat $beat
   * 
   * @return int
   */
  public function getIncrementTime(Beat $beat)
  {
    $duration = 0;

    if ($this->value > 0)
    {
      for ($v = 0; $v < $beat->countVoices(); $v++)
      {
        $voice = $beat->getVoice($v);

        if (!$voice->isEmpty())
        {
          $currentDuration = $voice->getDuration()->getTime();

          if ($duration == 0 || $currentDuration < $duration)
          {
            $duration = $currentDuration <= Duration::QUARTER_TIME
                      ? $currentDuration : Duration::QUARTER_TIME;
          }
        }
      }

      if ($duration > 0)
      {
        return round(($duration / 8.0) * (4.0 / $this->value));
      }
    }

    return 0;
  }

  /**
   * @return \PhpTabs\Model\Stroke
   */
  public function __clone()
  {
    $stroke = new Stroke();
    $stroke->copyFrom($this);
    return $stroke;
  }

  /**
   * @param \PhpTabs\Model\Stroke $stroke
   */
  public function copyFrom(Stroke $stroke)
  {
    $this->setValue($stroke->getValue());
    $this->setDirection($stroke->getDirection());
  }
}
