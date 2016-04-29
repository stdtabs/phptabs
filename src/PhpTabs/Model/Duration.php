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
    if((double)$this->value==0)
    {
      $this->value = PHP_INT_MAX; # workaround @todo FIXME when value is not set or 0

      \PhpTabs\Component\Log::add(__METHOD__  . ": Value ({$this->value})'"
        , 'ERROR\\MODEL');

      throw new \Exception("An overflow has been detected ["
        . __METHOD__ . "({$this->value})]");
    }

    $time = Duration::QUARTER_TIME * (4.0 / $this->value);

    if($this->dotted)
    {
      $time += $time / 2;
    }
    else if($this->doubleDotted)
    {
      $time += ($time / 4) * 3;
    }

    return intval($this->getDivision()->convertTime($time));
  }

  public static function fromTime($time, $minDuration = null, $diff = null)
  {
    if($minDuration === null && $diff === null)
    {
    $duration = new Duration();
    $duration->setValue(Duration::SIXTY_FOURTH);
    $duration->setDotted(false);
    $duration->setDoubleDotted(false);
    $duration->getDivision()->setEnters(3);
    $duration->getDivision()->setTimes(2);

    return self::fromTime($time, $duration);
    }
    else if($diff === null)
    {
      return self::fromTime($time, $duration, 10);
    }

    $duration = clone $minDuration;
    $tmpDuration = new Duration();
    $tmpduration->setValue(Duration::WHOLE);
    $tmpduration->setDotted(true);
    $finish = false;
    while(!$finish)
    {
      $tmpTime = $tmpduration->getTime();
      if($tmpTime - $diff <= $time)
      {
        if(abs($tmpTime - $time) < abs($duration->getTime() - $time))
        {
          $duration = clone $tmpduration;
        }
      }
      if($tmpduration->isDotted())
      {
        $tmpduration->setDotted(false);
      }
      else if($tmpduration->getDivision()->isEqual(DivisionType::NORMAL))
      {
        $tmpduration->getDivision()->setEnters(3);
        $tmpduration->getDivision()->setTimes(2);
      }
      else
      {
        $tmpduration->setValue($tmpduration->getValue() * 2);
        $tmpduration->setDotted(true);
        $tmpduration->getDivision()->setEnters(1);
        $tmpduration->getDivision()->setTimes(1);
      }
      if($tmpduration->getValue() > Duration::SIXTY_FOURTH)
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

  public function isEqual(Duration $d)
  {
    return ($this->getValue() == $d->getValue() 
      && $this->isDotted() == $d->isDotted() 
      && $this->isDoubleDotted() == $d->isDoubleDotted()
      && $this->getDivision()->isEqual($d->getDivision()));
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
