<?php

namespace PhpTabs\Reader\GuitarPro;

use PhpTabs\Component\ReaderInterface;

/**
 * Interface for Guitar Pro Readers
 */
interface GuitarProReaderInterface extends ReaderInterface
{
  const GP_BEND_SEMITONE = 25;
  const GP_BEND_POSITION = 60;

  /**
   * @return array of supported versions
   */
  public function getSupportedVersions();
}
