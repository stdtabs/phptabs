<?php

namespace PhpTabs\Component;

/**
 * Interface for the bridge readers
 */
interface ReaderInterface
{
  /**
   * @return \PhpTabs\Component\Tablature built from file read
   */
  public function getTablature();
}
