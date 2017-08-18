<?php

namespace PhpTabs\Music;

/**
 * Modelizes division between 2 notes
 */
class DivisionType
{
  private $enters;
  private $times;

  public function __construct()
  {
    $this->enters = 1;
    $this->times = 1;
  }

  /**
   * @return array
   */
  public function getEnters()
  {
    return $this->enters;
  }

  /**
   * @param array $enters
   */
  public function setEnters($enters)
  {
    $this->enters = $enters;
  }

  /**
   * @return array
   */
  public function getTimes()
  {
    return $this->times;
  }

  /**
   * @param array $times
   */
  public function setTimes($times)
  {
    $this->times = $times;
  }

  /**
   * @return int
   */
  public function convertTime($time)
  {
    return intval($time * $this->times / $this->enters);
  }

  /**
   * @param \PhpTabs\Music\DivisionType
   * 
   * @return bool
   */
  public function isEqual(DivisionType $divisionType)
  {
    return ($divisionType->getEnters() == $this->getEnters()) 
        && ($divisionType->getTimes()  == $this->getTimes());
  }

  /**
   * @return \PhpTabs\Music\DivisionType
   */
  public function __clone()
  {
    $divisionType = self::newDivisionType();
    $divisionType->copyFrom($this);
    return $divisionType;
  }

  /**
   * @param \PhpTabs\Music\DivisionType $divisionType
   */
  public function copyFrom(DivisionType $divisionType)
  {
    $this->setEnters($divisionType->getEnters());
    $this->setTimes($divisionType->getTimes());
  }

  /**
   * @return \PhpTabs\Music\DivisionType
   */
  public static function normal()
  {
    return self::newDivisionType(1, 1);
  }

  /**
   * @return \PhpTabs\Music\DivisionType
   */
  public static function triplet()
  {
    return self::newDivisionType(3, 2);
  }

  /**
   * @return array
   */
  public static function alteredDivisionTypes()
  {
    return array(
      self::newDivisionType(3,2),
      self::newDivisionType(5,4),
      self::newDivisionType(6,4),
      self::newDivisionType(7,4),
      self::newDivisionType(9,8),
      self::newDivisionType(10,8),
      self::newDivisionType(11,8),
      self::newDivisionType(12,8),
      self::newDivisionType(13,8),
    );
  }

  /**
   * @return \PhpTabs\Music\DivisionType
   */
  private static function newDivisionType($enters, $times)
  {
    $divisionType = new DivisionType();
    $divisionType->setEnters($enters);
    $divisionType->setTimes($times);
    return $divisionType;
  }

}
