<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Renderer\VexTab;

use Exception;
use PhpTabs\Component\Renderer\RendererHelper;
use PhpTabs\Component\Renderer\RendererInterface;
use PhpTabs\Music\Track;
use PhpTabs\Music\Song;

class VexTabRenderer extends RendererHelper
{
  /**
   * Song container
   * 
   * @var \PhpTabs\Music\Song
   */
  private $song;

  /**
   * Constructor
   *
   * @param  \PhpTabs\Music\Song $song
   */
  public function __construct(Song $song)
  {
    $this->song = $song;
  }

  /**
   * Dump a track, VexTab formatted
   * 
   * @param  int    $index
   * @return string A list of tabstaves, VexTab formatted
   * @api
   * @since 0.5.0
   */
  public function render($index)
  {
    $track = $this->song->getTrack($index);

    if (null === $track) {
      throw new Exception(
        'Track has not been found. Given:' . $index
      );
    }

    return $this->writeTrack($track);
  }

  /**
   * Convert a track as a VexTab text
   * 
   * @param  \PhpTabs\Music\Track $track
   * @return string
   */
  private function writeTrack(Track $track)
  {
    return (new VexTabTrackRenderer($this, $track))->render();
  }
}
