<?php

namespace PhpTabs\Model;

/**
 * @package Text
 */

class Text
{
  private $value;
  private $beat;

  public function __construct(){}

  public function getBeat()
  {
    return $this->beat;
  }

  public function setBeat(Beat $beat)
  {
    $this->beat = $beat;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function isEmpty()
  {
    return ($this->value == null || strlen($this->value) == 0);
  }

  public function copyFrom(Text $text)
  {
    $this->setValue($text->getValue());
  }

  public function __clone()
  {
    $text = new Text();
    $text->copyFrom($this);
    return $text;
  }
}
