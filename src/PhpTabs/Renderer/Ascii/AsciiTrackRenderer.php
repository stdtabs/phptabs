<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Renderer\Ascii;

use Exception;
use PhpTabs\Music\Measure;
use PhpTabs\Music\TabString;
use PhpTabs\Music\Track;
use PhpTabs\Music\Song;

class AsciiTrackRenderer
{
  private static $TONIC_NAMES = ["C","C#","D","D#","E","F","F#","G","G#","A","A#","B"];

  const MAX_LINE_LENGTH = 80;

  /**
   * Global track container
   * 
   * @var \PhpTabs\Music\Track
   */
  private $track;

  /**
   * Global writer
   * 
   * @var \PhpTabs\Renderer\Ascii\AsciiBase
   */
  private $writer;

  /**
   * Parent renderer
   * 
   * @var \PhpTabs\Renderer\Ascii\AsciiRenderer
   */
  private $parent;

  /**
   * Constructor
   *
   * @param  \PhpTabs\Renderer\Ascii\AsciiRenderer $renderer
   * @param  \PhpTabs\Music\Track $track
   */
  public function __construct(AsciiRenderer $renderer, Track $track)
  {
    $this->track  = $track;
    $this->writer = $renderer->getWriter();
    $this->parent = $renderer;
    
    if ($this->parent->getOption('maxLineLength') === null) {
      $this->parent->setOption('maxLineLength', self::MAX_LINE_LENGTH);
    }
  }

  /**
   * Dump a track, ASCII formatted
   * 
   * @param  int    $index
   * @return string A list of tabstaves, ASCII formatted
   * @api
   * @since 0.6.0
   */
  public function render($index = 0)
  {
    $this->writer->nextLine();

    if ($this->parent->getOption('trackHeader')) {
      $this->writer->drawStringLine(
        "Track "
        . $this->track->getNumber()
        . ": "
        . $this->track->getName()
      );
    }

    list($tuning, $maxTuningLength) = $this->getTuning();

    $nextMeasure  = 0;
    $measureCount = $this->track->countMeasures();
    $stringCount  = $this->track->countStrings();

    $eof = false;

    while (!$eof) {
      $this->writer->nextLine();
      $index = $nextMeasure;

      for ($i = 0; $i < $stringCount; $i++) {
        $string = $this->track->getString($i + 1);

        $this->writer->drawTuneSegment($tuning[$i], $maxTuningLength);

        for ($j = $index; $j < $measureCount; $j++) {
          $measure = $this->track->getMeasure($j);
          $this->writeMeasure($measure, $string);
          $nextMeasure = $j + 1;

          // Last measure
          $eof = $measureCount === $measure->getNumber();

          // Break line
          if ($this->writer->getPosX() > $this->parent->getOption('maxLineLength')) {
            break;
          }
        }

        // Close measure
        $this->writer->drawBarSegment();
        $this->writer->nextLine();
      }

      $this->writer->nextLine();
    }

    $this->writer->nextLine();
  }

  /**
   * Get tuning params
   * 
   * @return array($tuning, $maxTuningLength)
   */
  private function getTuning()
  {
    $tuning = array_fill(0, $this->track->countStrings(), null);
    $maxTuningLength = 1;

    foreach ($this->track->getStrings() as $index => $string) {
      $tuning[$index] = self::$TONIC_NAMES[
        ($string->getValue() % count(self::$TONIC_NAMES))
      ];
      $maxTuningLength = max($maxTuningLength, strlen($tuning[$index]));
    }

    return [$tuning, $maxTuningLength];
  }

  /**
   * Write a measure as an ASCII text
   * 
   * @param  \PhpTabs\Music\Measure   $measure
   * @param  \PhpTabs\Music\TabString $string
   */
  private function writeMeasure(Measure $measure, TabString $string)
  {
    (new AsciiMeasureRenderer($this->writer, $measure, $string))->render();
  }
}
