<?php

namespace PhpTabs\Component;

/**
 * Interface for the bridge Reader's classes
 */

interface ReaderInterface
{
  /**
   * @return Tablature built from file read
   */
  public function getTablature();
}
