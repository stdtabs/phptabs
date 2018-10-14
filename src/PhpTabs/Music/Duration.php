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

class Duration
{
  const QUARTER_TIME = 960;
  const WHOLE = 1;
  const HALF = 2;
  const QUARTER = 4;
  const EIGHTH = 8;
  const SIXTEENTH = 16;
  const THIRTY_SECOND = 32;
  const SIXTY_FOURTH = 64;

  private $value;
  private $divisionType;
  private $dotted       = false;
  private $doubleDotted = false;

  public function __construct()
  {
    $this->value        = Duration::QUARTER;
    $this->divisionType = new DivisionType();
  }

  /**
   * @return int
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
   * @return bool
   */
  public function isDotted()
  {
    return $this->dotted;
  }

  /**
   * @param bool $dotted
   */
  public function setDotted($dotted)
  {
    $this->dotted = (boolean)$dotted;
  }

  /**
   * @return bool
   */
  public function isDoubleDotted()
  {
    return (boolean)$this->doubleDotted;
  }

  /**
   * @param bool $doubleDotted
   */
  public function setDoubleDotted($doubleDotted)
  {
    $this->doubleDotted = (boolean)$doubleDotted;
  }

  /**
   * @return mixed
   */
  public function getDivision()
  {
    return $this->divisionType;
  }

  /**
   * @return mixed
   */
  public function getTime()
  {
    $time = Duration::QUARTER_TIME * (4.0 / $this->value);

    if ($this->dotted)
    {
      $time += $time / 2;
    }
    elseif ($this->doubleDotted)
    {
      $time += ($time / 4) * 3;
    }

    return $this->getDivision()->convertTime($time);
  }

  /**
   * @param int $time
   * @param null|\PhpTabs\Music\Duration $minDuration
   * @param null|int $diff
   * 
   * @return int
   */
  public static function fromTime($time, Duration $minDuration = null, $diff = null)
  {
    if (is_null($minDuration)) {
      $duration = new Duration();
      $duration->setValue(self::SIXTY_FOURTH);
      $duration->setDotted(false);
      $duration->setDoubleDotted(false);
      $duration->getDivision()->setEnters(3);
      $duration->getDivision()->setTimes(2);

      return self::fromTime($time, $duration);
    } elseif (is_null($diff)) {
      return self::fromTime($time, $minDuration, 10);
    }

    $duration = clone $minDuration;
    $tmpDuration = new Duration();
    $tmpDuration->setValue(self::WHOLE);
    $tmpDuration->setDotted(true);
    $finish = false;

    while (!$finish) {
      $tmpTime = $tmpDuration->getTime();
      if ($tmpTime - $diff <= $time)
      {
        if (abs($tmpTime - $time) < abs($duration->getTime() - $time))
        {
          $duration = clone $tmpDuration;
        }
      }

      if ($tmpDuration->isDotted())
      {
        $tmpDuration->setDotted(false);
      }
      elseif ($tmpDuration->getDivision()->isEqual(DivisionType::normal()))
      {
        $tmpDuration->getDivision()->setEnters(3);
        $tmpDuration->getDivision()->setTimes(2);
      }
      else
      {
        $tmpDuration->setValue($tmpDuration->getValue() * 2);
        $tmpDuration->setDotted(true);
        $tmpDuration->getDivision()->setEnters(1);
        $tmpDuration->getDivision()->setTimes(1);
      }

      if ($tmpDuration->getValue() > self::SIXTY_FOURTH)
      {
        $finish = true;
      }
    }

    return $duration;
  }

  /**
   * @return int
   */
  public function getIndex()
  {
    $index = 0;
    $value = $this->value;
    while (($value = ($value >> 1) ) > 0)
    {
      $index++;
    }

    return $index;
  }

  /**
   * @param \PhpTabs\Music\Duration $duration
   * 
   * @return bool
   */
  public function isEqual(Duration $duration)
  {
    return ($this->getValue() == $duration->getValue() 
      && $this->isDotted() == $duration->isDotted() 
      && $this->isDoubleDotted() == $duration->isDoubleDotted()
      && $this->getDivision()->isEqual($duration->getDivision()));
  }

  /**
   * @return \PhpTabs\Music\Duration
   */
  public function __clone()
  {
    $duration = new Duration();
    $duration->copyFrom($this);
    return $duration;
  }

  /**
   * @param \PhpTabs\Music\Duration $duration
   */
  public function copyFrom(Duration $duration)
  {
    $this->setValue($duration->getValue());
    $this->setDotted($duration->isDotted());
    $this->setDoubleDotted($duration->isDoubleDotted());
    $this->getDivision()->copyFrom($duration->getDivision());
  }
}
