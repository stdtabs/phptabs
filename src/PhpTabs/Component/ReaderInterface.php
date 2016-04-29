<?php

namespace PhpTabs\Component;


/**
 * Interface for Reader classes
 */

interface ReaderInterface
{
  /**
   * @return Tablature built from file read
   */
  public function getTablature();
}
