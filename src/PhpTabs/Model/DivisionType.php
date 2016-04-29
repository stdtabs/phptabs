<?php

namespace PhpTabs\Model;


class DivisionType
{
	public static function NORMAL()
  {
    return self::newDivisionType(1,1);
  }
	
	public static function TRIPLET()
  {
    return self::newDivisionType(3,2);
	}

	public static function ALTERED_DIVISION_TYPES()
  {
    return array(
      newDivisionType(3,2),
      newDivisionType(5,4),
      newDivisionType(6,4),
      newDivisionType(7,4),
      newDivisionType(9,8),
      newDivisionType(10,8),
      newDivisionType(11,8),
      newDivisionType(12,8),
      newDivisionType(13,8),
    );
	}

	private $enters;
	private $times;
	
	public function __construct()
  {
		$this->enters = 1;
		$this->times = 1;
	}
	
	public function getEnters()
  {
		return $this->enters;
	}
	
	public function setEnters($enters)
  {
		$this->enters = $enters;
	}
	
	public function getTimes()
  {
		return $this->times;
	}
	
	public function setTimes($times)
  {
		$this->times = $times;
	}
	
	public function convertTime($time)
  {
		return $time * $this->times / $this->enters;
	}
	
	public function isEqual(DivisionType $divisionType)
  {
		return ($divisionType->getEnters() == $this->getEnters() && $divisionType->getTimes() == $this->getTimes());
	}
	
	public function __clone()
  {
		$divisionType = self::newDivisionType();
		$divisionType->copyFrom($this);
		return $divisionType;
	}
	
	public function copyFrom(DivisionType $divisionType)
  {
		$this->setEnters($divisionType->getEnters());
		$this->setTimes($divisionType->getTimes());
	}
	
	private static function newDivisionType($enters, $times)
  {
		$divisionType = new DivisionType();
		$divisionType->setEnters($enters);
		$divisionType->setTimes($times);
		return $divisionType;
	}
	
}
