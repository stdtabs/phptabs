<?php

namespace PhpTabs\Model;

/**
 * @package Scale
 */

class Scale
{
  private $notes = array(); // 12

  private $key;

  public function __construct()
  {
    $this->clear();
  }

  public function setKey($key)
  {
    $this->key = $key;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function setNote($note, $on)
  {
    $this->notes[$note] = $on;
  }

  public function getNote($note)
  {
    return $this->notes[(($note + (12 - $this->key)) % 12)];
  }

  public function clear()
  {
    $this->setKey(0);

    for($i=0; $i<count($this->notes); $i++)
    {
      $this->setNote($i,false);
    }
  }
}
