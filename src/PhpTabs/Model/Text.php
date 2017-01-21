<?php

namespace PhpTabs\Model;

class Text
{
  private $value;
  private $beat;

  public function __construct(){}

  /**
   * @return \PhpTabs\Model\Beat
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param \PhpTabs\Model\Beat $beat
   */
  public function setBeat(Beat $beat)
  {
    $this->beat = $beat;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * @return bool
   */
  public function isEmpty()
  {
    return ($this->value == null || strlen($this->value) == 0);
  }

  /**
   * @param \PhpTabs\Model\Text $text
   */
  public function copyFrom(Text $text)
  {
    $this->setValue($text->getValue());
  }

  /**
   * @return \PhpTabs\Model\Text
   */
  public function __clone()
  {
    $text = new Text();
    $text->copyFrom($this);
    return $text;
  }
}
