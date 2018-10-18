<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Music;

/**
 * @uses Chord
 * @uses Duration
 * @uses Measure
 * @uses Stroke
 * @uses Text
 * @uses Voice
 */
class Beat
{
  /** @const MAX_VOICES Number of voices to set */
  const MAX_VOICES = 2;

  private $start = Duration::QUARTER_TIME;
  private $measure;
  private $chord;
  private $text;
  private $voices = [];
  private $stroke;

  public function __construct()
  {
    $this->stroke = new Stroke();

    for ($i = 0; $i < self::MAX_VOICES; $i++) {
      $this->setVoice($i, new Voice($i));
    }
  }

  /**
   * @return \PhpTabs\Music\Measure
   */
  public function getMeasure()
  {
    return $this->measure;
  }

  /**
   * @param \PhpTabs\Music\Measure $measure
   */
  public function setMeasure(Measure $measure)
  {
    $this->measure = $measure;
  }

  /**
   * @return int
   */
  public function getStart()
  {
    return $this->start;
  }

  /**
   * @param int $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }

  /**
   * @param int $index
   * @param \PhpTabs\Music\Voice $voice
   */
  public function setVoice($index, Voice $voice)
  {
    if ($index >= 0) {
      $this->voices[$index] = $voice;
      $this->voices[$index]->setBeat($this);
    }
  }

  /**
   * @param  int $index
   * @return \PhpTabs\Music\Voice
   */
  public function getVoice($index)
  {
    if (isset($this->voices[$index])) {
      return $this->voices[$index];
    }

    throw new Exception(
      sprintf(
        'Index %s does not exist',
        $index
      )
    );
  }

  /**
   * @return \PhpTabs\Music\Voice[]
   */
  public function getVoices()
  {
    return $this->voices;
  }

  /**
   * @return int
   */
  public function countVoices()
  {
    return count($this->voices);
  }

  /**
   * @param \PhpTabs\Music\Chord $chord
   */
  public function setChord(Chord $chord)
  {
    $this->chord = $chord;
    $this->chord->setBeat($this);
  }

  /**
   * @return \PhpTabs\Music\Chord
   */
  public function getChord()
  {
    return $this->chord;
  }

  public function removeChord()
  {
    $this->chord = null;
  }

  /**
   * @return \PhpTabs\Music\Text
   */
  public function getText()
  {
    return $this->text;
  }

  /**
   * @param \PhpTabs\Music\Text $text
   */
  public function setText(Text $text)
  {
    $this->text = $text;
    $this->text->setBeat($this);
  }

  public function removeText()
  {
    $this->text = null;
  }

  /**
   * @return bool
   */
  public function isChordBeat()
  {
    return $this->chord !== null;
  }

  /**
   * @return bool
   */
  public function isTextBeat()
  {
    return $this->text !== null;
  }

  /**
   * @return \PhpTabs\Music\Stroke
   */
  public function getStroke()
  {
    return $this->stroke;
  }

  /**
   * @return bool
   */
  public function isRestBeat()
  {
    for ($v = 0; $v < $this->countVoices(); $v++) {
      $voice = $this->getVoice($v);

      if (!$voice->isEmpty() && !$voice->isRestVoice()) {
        return false;
      }
    }

    return true;
  }

  /**
   * @return \PhpTabs\Music\Beat
   */
  public function __clone()
  {
    $beat = new Beat();
    $beat->setStart($this->getStart());
    $beat->getStroke()->copyFrom($this->getStroke());

    for ($i = 0; $i < count($this->voices); $i++) {
      $beat->setVoice($i, clone $this->voices[$i]);
    }
    
    if ($this->chord !== null) {
      $beat->setChord(clone $this->chord);
    }

    if ($this->text !== null) {
      $beat->setText(clone $this->text);
    }

    return $beat;
  }
}
