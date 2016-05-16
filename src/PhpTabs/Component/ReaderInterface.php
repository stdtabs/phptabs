<?php

namespace PhpTabs\Component;

/**
 * Interface for the bridge readers
 */
interface ReaderInterface
{
  /**
   * @return Tablature built from file read
   */
  public function getTablature();
}
