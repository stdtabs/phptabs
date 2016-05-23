<?php

namespace PhpTabs\Model;

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
  private $dotted;
  private $doubleDotted;
  private $divisionType;	

  /**
   * Constructor
   * 
   * @return void
   */
  public function __construct()
  {
    $this->value = Duration::QUARTER;
    $this->dotted = false;
    $this->doubleDotted = false;
    $this->divisionType = new DivisionType();
  }

  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function isDotted()
  {
    return $this->dotted;
  }

  public function setDotted($dotted)
  {
    $this->dotted = (boolean)$dotted;
  }

  public function isDoubleDotted()
  {
    return (boolean)$this->doubleDotted;
  }

  public function setDoubleDotted($doubleDotted)
  {
    $this->doubleDotted = (boolean)$doubleDotted;
  }

  public function getDivision()
  {
    return $this->divisionType;
  }

  public function getTime()
  {
    $time = Duration::QUARTER_TIME * (4.0 / $this->value);

    if($this->dotted)
    {
      $time += $time / 2;
    }
    else if($this->doubleDotted)
    {
      $time += ($time / 4) * 3;
    }

    return $this->getDivision()->convertTime($time);
  }

  public static function fromTime($time, $minDuration = null, $diff = null)
  {
    if($minDuration === null && $diff === null)
    {
      $duration = new Duration();
      $duration->setValue(self::SIXTY_FOURTH);
      $duration->setDotted(false);
      $duration->setDoubleDotted(false);
      $duration->getDivision()->setEnters(3);
      $duration->getDivision()->setTimes(2);

      return self::fromTime($time, $duration);
    }
    else if($diff === null)
    {
      return self::fromTime($time, $minDuration, 10);
    }

    $duration = clone $minDuration;
    $tmpDuration = new Duration();
    $tmpDuration->setValue(self::WHOLE);
    $tmpDuration->setDotted(true);
    $finish = false;

    while(!$finish)
    {
      $tmpTime = $tmpDuration->getTime();
      if($tmpTime - $diff <= $time)
      {
        if(abs($tmpTime - $time) < abs($duration->getTime() - $time))
        {
          $duration = clone $tmpDuration;
        }
      }

      if($tmpDuration->isDotted())
      {
        $tmpDuration->setDotted(false);
      }
      else if($tmpDuration->getDivision()->isEqual(DivisionType::normal()))
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

      if($tmpDuration->getValue() > self::SIXTY_FOURTH)
      {
        $finish = true;
      }
    }

    return $duration;
  }

  public function getIndex()
  {
    $index = 0;
    $value = $this->value;
    while(($value = ($value >> 1) ) > 0)
      $index ++;

    return $index;
  }

  public function isEqual(Duration $duration)
  {
    return ($this->getValue() == $duration->getValue() 
      && $this->isDotted() == $duration->isDotted() 
      && $this->isDoubleDotted() == $duration->isDoubleDotted()
      && $this->getDivision()->isEqual($duration->getDivision()));
  }

  public function __clone()
  {
    $duration = new Duration();
    $duration->copyFrom($this);
    return $duration;
  }

  public function copyFrom(Duration $duration)
  {
    $this->setValue($duration->getValue());
    $this->setDotted($duration->isDotted());
    $this->setDoubleDotted($duration->isDoubleDotted());
    $this->getDivision()->copyFrom($duration->getDivision());
  }
}
