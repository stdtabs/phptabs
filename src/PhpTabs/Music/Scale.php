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

class Scale
{
  private $notes = array(); // 12
  private $key;

  public function __construct()
  {
    $this->clear();
  }

  /**
   * @param int $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }

  /**
   * @return int
   */
  public function getKey()
  {
    return $this->key;
  }

  /**
   * @param int $note
   * @param int|boolean $on
   */
  public function setNote($note, $on)
  {
    $this->notes[$note] = $on;
  }

  /**
   * @param int $note
   * 
   * @return int
   */
  public function getNote($note)
  {
    return $this->notes[(($note + (12 - $this->key)) % 12)];
  }

  public function clear()
  {
    $this->setKey(0);

    for ($i = 0; $i < count($this->notes); $i++)
    {
      $this->setNote($i, false);
    }
  }
}
