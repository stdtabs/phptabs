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
use PhpTabs\Component\Renderer\RendererInterface;
use PhpTabs\Music\Beat;
use PhpTabs\Music\Channel;
use PhpTabs\Music\Duration;
use PhpTabs\Music\Measure;
use PhpTabs\Music\Note;
use PhpTabs\Music\Track;

class VexTabTrackRenderer
{
  /**
   * A basic stave template
   * 
   * @var string
   */
  private $staveTpl = "tabstave%s";

  /**
   * Global options template
   * 
   * @var string
   */
  private $optionsTpl = "options%s\n\n";

  /**
   * Durations translation
   * 
   * @var array
   */
  private $defDuration = [
    1   => 'w',
    2   => 'h',
    4   => 'q',
    8   => '8',
    16  => '16',
    32  => '32',
    64  => '64'
  ];

  /**
   * Clefs translation
   * 
   * @var array
   */
  private $defClef = [
    1   => 'treble',
    2   => 'bass',
    3   => 'tenor',
    4   => 'alto'
  ];

  /**
   * Global renderer
   * 
   * @var \PhpTabs\Component\Renderer\RendererInterface
   */
  private $renderer;

  /**
   * Stave content
   * 
   * @var string
   */
  private $staves;

  /**
   * Track
   * 
   * @var \PhpTabs\Music\Track
   */
  private $track;

  /**
   * Options
   * 
   * @var \PhpTabs\Renderer\VexTab\VexTabOptions
   */
  private $options;

  /**
   * Channel
   * 
   * @var \PhpTabs\Music\Channel
   */
  private $channel;

  /**
   * @param \PhpTabs\Component\Renderer\RendererInterface $renderer
   * @param \PhpTabs\Music\Track                          $track
   */
  public function __construct(RendererInterface $renderer, Track $track)
  {
    if (!$track->countMeasures()) {
      throw new Exception(
        'Track has not any measures.'
      );
    }

    $this->options = new VexTabOptions($renderer);

    $this->renderer = $renderer;
    $this->track    = $track;
    $this->channel  = $track
      ->getSong()
      ->getChannelById(
        $track->getChannelId()
    );

    // Global options config
    $this->initStaves(
      $track->getMeasure(0)
    );

    // Start to write measures
    $this->repeatOpen       = false;
    $this->doubleRepeatOpen = false;
    $this->lastBeatContext  = new BeatContext(new Beat());
    $this->line             = 0;

    foreach ($track->getMeasures() as $measure) {
      $this->renderMeasure($measure);
    }
  }

  /**
   * Get a stave string
   *
   * @return string
   */
  public function render()
  {
    return $this->staves;
  }

  /**
   * Append a measure
   * 
   * @param  \PhpTabs\Music\Measure $measure
   */
  private function renderMeasure(Measure $measure)
  {
    if (
      ($measure->getNumber() - 1) % 
      $this->renderer->getOption('measures_per_stave', 1) == 0
    ) {

      $this->line++;
      $this->staves .= $measure->getNumber() > 1 
        ? sprintf(
            "\n\n{$this->staveTpl}", 
            $this->options->render('stave')
          ) 
        : '';

      $this->staves .= "\nnotes ";
    }

    // bar / repeat
    if ($measure->isRepeatOpen()) {
      $this->staves    .= '=|: ';
      $this->repeatOpen = true;
    }

    $this->writeMeasure($measure);

    // ./ bar / repeat 
    if (($measure->getRepeatClose() > 0 || $this->isLastMeasure($measure))
      && $this->repeatOpen
    ) {
      $this->staves      .= '=:|';
      $this->repeatOpen   = false;
    } elseif (($measure->getRepeatClose() > 0 || $this->isLastMeasure($measure))
      && $this->doubleRepeatOpen
    ) {
      $this->staves          .= '=::';
      $this->doubleRepeatOpen = false;
    } else {
      // bugfix: time notation creates an offset which is not
      //         represented in a tab 
      $this->staves .= $this->line > 1 
        || $measure->getNumber() !== $this->renderer->getOption('measures_per_stave', 1)
        ? '|' : ''; 
    }
  }

  /**
   * @param  \PhpTabs\Music\Measure $measure
   * @return bool
   */
  private function isLastMeasure(Measure $measure)
  {
    return $measure->getNumber() == $this->track->countMeasures();
  }

  /**
   * Start a list of staves
   * 
   * @param \PhpTabs\Music\Measure $measure
   */
  private function initStaves(Measure $measure)
  {
    $numerator    = $measure->getTimeSignature()->getNumerator();
    $denominator  = $measure->getTimeSignature()->getDenominator()->getValue();

    $this->options->add('tempo', $measure->getTempo()->getValue());
    $this->options->add('clef',  $this->getClefName($measure));

    $this->staves = sprintf(
      $this->optionsTpl,
      $this->options->render('globals')
    );

    // stave config
    $this->staves .= sprintf(
      $this->staveTpl,
      $this->options->render('stave')
    );

    $this->staves .= " time=$numerator/$denominator\n";
  }

  /**
   * Write a measure
   * 
   * @param \PhpTabs\Music\Measure $measure
   */
  private function writeMeasure(Measure $measure)
  {
    $this->lastDuration   = '';

    foreach ($measure->getBeats() as $beat) {
      $this->beat        = $beat;
      $this->beatContext = new BeatContext($beat);

      // Prepare duration string
      $this->tmpDuration = $this->getDuration(
        $beat->getVoice(0)->getDuration()
      );

      $this->writeBeat();
    }
  }

  /**
   * Write a beat
   */
  private function writeBeat()
  {
    $this->renderDuration();

    /**
     * Chord beat
     */
    if ($this->beatContext->isChordBeat()) {
      $this->staves .= $this->renderChordBeat();

    /**
     * Single note beat
     */
    } elseif (!$this->beat->isRestBeat()) {
      $this->staves .= $this->renderSingleNoteBeat(
        $this->beat->getVoice(0)->getNote(0)
      );
    /**
     * Rest beat
     */
    } elseif ($this->beat->isRestBeat()) {
      $this->staves .= $this->renderRestBeat();
    }

    $this->lastBeatContext = $this->beatContext;
  }

  /**
   * Append duration if there is any change
   */
  private function renderDuration()
  {
    if ($this->lastDuration == ''
        || $this->lastDuration !== $this->tmpDuration
    ) {
      $this->staves    .= $this->tmpDuration;
    }

    $this->lastDuration = $this->tmpDuration;
  }

  /**
   * Render a rest beat
   * 
   * @return string
   */
  protected function renderRestBeat()
  {
    return '## ';
  }

  /**
   * Render a chord beat
   * 
   * @return string
   */
  private function renderChordBeat()
  {
    $stack = [];

    foreach ($this->beat->getVoice(0)->getNotes() as $note) {
      $stack[] = $this->renderSingleNoteBeat($note);
    }

    return sprintf(
      '(%s)%s %s',
      implode('.', $stack),
      $this->beatContext->getChordSuffix(),
      $this->beatContext->getTuplet($this->lastBeatContext)
    );
  }

  /**
   * Render a note value and effects
   * 
   * @param  \PhpTabs\Music\Note $note
   * @return string
   */
  private function renderSingleNoteBeat(Note $note)
  {
    return sprintf(
        '%s%s%s%s/%s%s%s',
        $this->lastBeatContext->getPrevPrefix($note),
        $this->lastBeatContext->getPrevPrefix($note) == '' 
          ? $this->beatContext->getPrefix($note) : '',
        $note->getEffect()->isDeadNote()? 'X' : $note->getValue(),
        $this->beatContext->getSuffix($note),
        $note->getString(),
        !$this->beatContext->isChordBeat() ? ' ' : '',
        !$this->beatContext->isChordBeat() 
          ? $this->beatContext->getTuplet($this->lastBeatContext) : ''
    );
  }

  /**
   * Get corresponding clef name
   * 
   * @param  \PhpTabs\Music\Measure $measure
   * @return string
   * 
   * @throws \Exception if clef name does not exist
   */
  private function getClefName(Measure $measure)
  {
    if ($this->channel->isPercussionChannel()) {
      return 'percussion';
    }

    if (isset($this->defClef[$measure->getClef()])) {
      return $this->defClef[$measure->getClef()];
    }

    throw new Exception(
      'Clef name was not found. Given:'
      . $measure->getClef()
    ); // @codeCoverageIgnore
  }

  /**
   * Format a VexTab duration
   * 
   * @param  \PhpTabs\Music\Duration $duration
   * @return string
   */
  private function getDuration(Duration $duration)
  {
    if (!isset($this->defDuration[$duration->getValue()])) {
      throw new Exception (
        'Duration value is not defined. Given:'
        . $duration->getValue()
      ); // @codeCoverageIgnore
    }

    return sprintf(
      ':%s%s%s ',
      $this->defDuration[
        $duration->getValue()
      ],
      $duration->isDotted()       ? 'd'  : '',
      $duration->isDoubleDotted() ? 'dd' : ''
    );
  }
}
