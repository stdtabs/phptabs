<?php

namespace PhpTabs\Reader\GuitarPro;


/**
 * Interface for Guitar Pro Reader classes
 */

interface GuitarProReaderInterface
{
 	const GP_BEND_SEMITONE = 25;
	const GP_BEND_POSITION = 60;

  /**
   * @return array supported versions
   */
  public function getSupportedVersions();

}
