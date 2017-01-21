<?php

namespace PhpTabs\Reader\GuitarPro\Helper;

use PhpTabs\Model\Beat;
use PhpTabs\Model\Measure;
use PhpTabs\Model\NoteEffect;
use PhpTabs\Model\Tempo;
use PhpTabs\Model\Track;

class GuitarPro5Beat extends AbstractReader
{
  /**
   * Reads some Beat informations
   * 
   * @param integer $start
   * @param \PhpTabs\Model\Measure $measure
   * @param \PhpTabs\Model\Track $track
   * @param \PhpTabs\Model\Tempo $tempo
   * @param integer $voiceIndex
   * 
   * @return integer $time A duration time
   */
  public function readBeat($start, Measure $measure, Track $track, Tempo $tempo, $voiceIndex)
  {
    $flags = $this->reader->readUnsignedByte();

    $beat = $measure->getBeatByStart($start);
    $voice = $beat->getVoice($voiceIndex);

    if (($flags & 0x40) != 0)
    {
      $beatType = $this->reader->readUnsignedByte();
      $voice->setEmpty(($beatType & 0x02) == 0);
    }

    $duration = $this->reader->factory('GuitarProDuration')->readDuration($flags);
    $effect = new NoteEffect();

    if (($flags & 0x02) != 0)
    {
      $this->reader->factory('GuitarPro5Chord')->readChord($track->countStrings(), $beat);
    }

    if (($flags & 0x04) != 0) 
    {
      $this->reader->factory('GuitarProText')->readText($beat);
    }

    if (($flags & 0x08) != 0)
    {
      $this->reader->factory('GuitarPro4BeatEffects')->readBeatEffects($beat, $effect);
    }

    if (($flags & 0x10) != 0)
    {
      $this->reader->factory('GuitarPro5MixChange')->readMixChange($tempo);
    }

    $stringFlags = $this->reader->readUnsignedByte();

    for ($i = 6; $i >= 0; $i--)
    {
      if (($stringFlags & (1 << $i)) != 0 && (6 - $i) < $track->countStrings())
      {
        $string = clone $track->getString( (6 - $i) + 1 );
        $note = $this->reader->factory('GuitarPro5Note')->readNote($string, $track, clone $effect);
        $voice->addNote($note);
      }

      $voice->getDuration()->copyFrom($duration);
    }

    $this->reader->skip();

    if (($this->reader->readByte() & 0x08) != 0)
    {
      $this->reader->skip();
    }

    return !$voice->isEmpty() ? $duration->getTime() : 0;
  }
}
