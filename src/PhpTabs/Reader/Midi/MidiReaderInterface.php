<?php

namespace PhpTabs\Reader\Midi;

use PhpTabs\Component\ReaderInterface;

/**
 * Interface for Midi Reader classes
 */
interface MidiReaderInterface extends ReaderInterface
{
  const	HEADER_LENGTH = 6;
  const HEADER_MAGIC = 0x4d546864;
  const TRACK_MAGIC = 0x4d54726b;

  /** Sequence */
  const PPQ = 0.0;
  const SMPTE_24 = 24.0;
  const SMPTE_25 = 25.0;
  const SMPTE_30DROP = 29.97;
  const SMPTE_30 = 30.0;

  /**
   * @return array of supported versions
   */
  public function getSupportedVersions();
}
