<?php

namespace PhpTabs\Component;

interface WriterInterface
{
  /**
   * @return string Data built from a writer
   */
  public function getContent();
}
