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

class Text
{
  private $value;
  private $beat;

  /**
   * @return \PhpTabs\Music\Beat
   */
  public function getBeat()
  {
    return $this->beat;
  }

  /**
   * @param \PhpTabs\Music\Beat $beat
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
   * @param \PhpTabs\Music\Text $text
   */
  public function copyFrom(Text $text)
  {
    $this->setValue($text->getValue());
  }

  /**
   * @return \PhpTabs\Music\Text
   */
  public function __clone()
  {
    $text = new Text();
    $text->copyFrom($this);
    return $text;
  }
}
