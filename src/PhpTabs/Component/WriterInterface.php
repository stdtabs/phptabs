<?php

namespace PhpTabs\Component;

/**
 * Interface for the bridge writers
 */
interface WriterInterface
{
  /**
   * @return string Data built from a writer
   */
  public function getContent();
}
