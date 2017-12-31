<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\GuitarPro;

use Exception;
use PhpTabs\Music\Beat;
use PhpTabs\Music\Channel;
use PhpTabs\Music\Chord;
use PhpTabs\Music\DivisionType;
use PhpTabs\Music\Duration;
use PhpTabs\Music\Marker;
use PhpTabs\Music\Measure;
use PhpTabs\Music\MeasureHeader;
use PhpTabs\Music\Song;
use PhpTabs\Music\Stroke;
use PhpTabs\Music\Tempo;
use PhpTabs\Music\Text;
use PhpTabs\Music\TimeSignature;
use PhpTabs\Music\Track;
use PhpTabs\Music\Velocities;
use PhpTabs\Music\Voice;
use PhpTabs\Reader\GuitarPro\GuitarProReaderInterface as GprInterface;

class GuitarPro5Writer extends GuitarProWriterBase
{
  /** @constant version */
  const VERSION = 'FICHIER GUITAR PRO v5.00';

  private $setUpLines = array(
    '%TITLE%',
    '%SUBTITLE%',
    '%ARTIST%',
    '%ALBUM%',
    'Words by %WORDS%',
    'Music by %MUSIC%',
    'Words & Music by %WORDSMUSIC%',
    'Copyright %COPYRIGHT%',
    'All Rights Reserved - International Copyright Secured',
    'Page %N%/%P%',
    'Moderate'
  );

  /**
   * @param \PhpTabs\Music\Song $song
   */
  public function __construct(Song $song)
  {
    parent::__construct();

    if ($song->isEmpty()) {
      throw new Exception('Song is empty');
    }

    $this->configureChannelRouter($song);
    $header = $song->getMeasureHeader(0);
    $this->writeStringByte(self::VERSION, 30);
    $this->writeInformations($song);
    $this->writeLyrics($song);
    $this->writeSetup();
    $this->writeInt($header->getTempo()->getValue());
    $this->writeInt(0);
    $this->writeByte(0);
    $this->getWriter('ChannelWriter')->writeChannels($song);

    for ($i = 0; $i < 42; $i++) {
      $this->writeByte(255);
    }

    $this->writeInt($song->countMeasureHeaders());
    $this->writeInt($song->countTracks());
    $this->writeMeasureHeaders($song);
    $this->writeTracks($song);
    $this->skipBytes(2);
    $this->writeMeasures($song, clone $header->getTempo());   
  }

  /**
   * @param \PhpTabs\Music\Voice $voice
   * @param \PhpTabs\Music\Beat $beat
   * @param \PhpTabs\Music\Measure $measure
   * @param bool $changeTempo
   */
  private function writeBeat(Voice $voice, Beat $beat, Measure $measure, $changeTempo)
  {
    $duration = $voice->getDuration();

    $effect = $this->getWriter('BeatWriter')->createEffect($voice);

    $flags = 0;

    if ($duration->isDotted() || $duration->isDoubleDotted()) 
    {
      $flags |= 0x01;
    }

    if ($voice->getIndex() == 0 && $beat->isChordBeat())
    {
      $flags |= 0x02;
    }

    if ($voice->getIndex() == 0 && $beat->isTextBeat())
    {
      $flags |= 0x04;
    }

    if ($beat->getStroke()->getDirection() != Stroke::STROKE_NONE)
    {
      $flags |= 0x08;
    }
    elseif ($effect->isTremoloBar() 
          || $effect->isTapping() 
          || $effect->isSlapping() 
          || $effect->isPopping() 
          || $effect->isFadeIn())
    {
      $flags |= 0x08;
    }

    if ($changeTempo)
    {
      $flags |= 0x10;
    }

    if (!$duration->getDivision()->isEqual(DivisionType::normal()))
    {
      $flags |= 0x20;
    }

    if ($voice->isEmpty() || $voice->isRestVoice())
    {
      $flags |= 0x40;
    }

    $this->writeUnsignedByte($flags);

    if (($flags & 0x40) != 0)
    {
      $this->writeUnsignedByte($voice->isEmpty() ? 0 : 0x02);
    }

    $this->writeByte($this->parseDuration($duration));

    if (($flags & 0x20) != 0)
    {
      $this->writeInt($duration->getDivision()->getEnters());
    }

    if (($flags & 0x02) != 0)
    {
      $this->writeChord($beat->getChord());
    }

    if (($flags & 0x04) != 0)
    {
      $this->writeText($beat->getText());
    }

    if (($flags & 0x08) != 0)
    {
      $this->getWriter('BeatEffectWriter')
           ->writeBeatEffects($beat, $effect);
    }

    if (($flags & 0x10) != 0)
    {
      $this->writeMixChange($measure->getTempo());
    }

    $stringFlags = 0;

    if (!$voice->isRestVoice())
    {
      for ($i = 0; $i < $voice->countNotes(); $i++)
      {
        $playedNote = $voice->getNote($i);
        $string = (7 - $playedNote->getString());
        $stringFlags |= (1 << $string);
      }
    }

    $this->writeUnsignedByte($stringFlags);

    for ($i = 6; $i >= 0; $i--)
    {
      if (($stringFlags & (1 << $i)) != 0)
      {
        for ($n = 0; $n < $voice->countNotes(); $n++)
        {
          $playedNote = $voice->getNote($n);
          if ($playedNote->getString() == (6 - $i + 1))
          {
            $this->getWriter('Note5Writer')->writeNote($playedNote);
            break;
          }
        }
      }
    }

    $this->skipBytes(2);
  }

  /**
   * @param \PhpTabs\Music\Chord $chord
   */
  private function writeChord(Chord $chord)
  {
    $this->writeBytes(
      array(
         1,  1,  0,  0, 0, 12, 0, 0,
        -1, -1, -1, -1, 0,  0, 0, 0, 0
      )
    );

    $this->writeStringByte($chord->getName(), 21);
    $this->skipBytes(4);
    $this->writeInt($chord->getFirstFret());

    for ($i = 0; $i < 7; $i++) {
      $this->writeInt($i < $chord->countStrings() ? $chord->getFretValue($i) : -1);
    }

    $this->skipBytes(32);
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeInformations(Song $song)
  {
    $this->writeStringByteSizeOfInteger($song->getName());
    $this->writeStringByteSizeOfInteger('');
    $this->writeStringByteSizeOfInteger($song->getArtist());
    $this->writeStringByteSizeOfInteger($song->getAlbum());
    $this->writeStringByteSizeOfInteger($song->getAuthor());
    $this->writeStringByteSizeOfInteger('');
    $this->writeStringByteSizeOfInteger($song->getCopyright());
    $this->writeStringByteSizeOfInteger($song->getWriter());
    $this->writeStringByteSizeOfInteger('');

    $comments = $this->toCommentLines($song->getComments());
    $this->writeInt(count($comments));
    for ($i = 0; $i < count($comments); $i++) {
      $this->writeStringByteSizeOfInteger($comments[$i]);
    }
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeLyrics(Song $song)
  {
    $lyricTrack = null;
    $tracks = $song->getTracks();

    foreach ($tracks as $track) {
      if (!$track->getLyrics()->isEmpty()) {
        $lyricTrack = $track;
        break;
      }
    }

    $this->writeInt($lyricTrack == null ? 0 : $lyricTrack->getNumber());
    $this->writeInt($lyricTrack == null ? 0 : $lyricTrack->getLyrics()->getFrom());
    $this->writeStringInteger(
      $lyricTrack == null ? '' : $lyricTrack->getLyrics()->getLyrics()
    );

    for ($i = 0; $i < 4; $i++) {
      $this->writeInt($lyricTrack === null ? 0 : 1);
      $this->writeStringInteger('');
    }
  }

  /**
   * @param \PhpTabs\Music\Marker $marker
   */
  private function writeMarker(Marker $marker)
  {
    $this->writeStringByteSizeOfInteger($marker->getTitle());
    $this->writeColor($marker->getColor());
  }

  /**
   * @param \PhpTabs\Music\Measure $measure
   * @param bool $changeTempo
   */
  private function writeMeasure(Measure $measure, $changeTempo)
  {
    for ($v = 0; $v < 2; $v++) {
      $voices = array();

      for ($m = 0; $m < $measure->countBeats(); $m++) {
        $beat = $measure->getBeat($m);
        if ($v < $beat->countVoices()) {
          $voice = $beat->getVoice($v);
          if (!$voice->isEmpty()) {
            $voices[] = $voice;
          }
        }
      }

      if (count($voices) > 0) {
        $this->writeInt(count($voices));
        for ($i = 0; $i < count($voices); $i++) {
          $voice = $voices[$i];
          $this->writeBeat($voice, $voice->getBeat(), $measure, $changeTempo && $i == 0);					
        }
      } else {
        $count = $measure->getTimeSignature()->getNumerator();
        $beat = new Beat();

        if ($v < $beat->countVoices()) {
          $voice = $beat->getVoice($v);
          $voice->getDuration()->setValue($measure->getTimeSignature()->getDenominator()->getValue());
          $voice->setEmpty(true);

          $this->writeInt($count);
          for ($i = 0; $i < $count; $i++) {
            $this->writeBeat($voice, $voice->getBeat(), $measure, $changeTempo && $i == 0);
          }
        }
      }
    }
  }

  /**
   * @param \PhpTabs\Music\MeasureHeader $measure
   * @param \PhpTabs\Music\TimeSignature $timeSignature
   */
  private function writeMeasureHeader(MeasureHeader $measure, TimeSignature $timeSignature)
  {
    $flags = 0;

    if ($measure->getNumber() == 1) {
      $flags |= 0x40;
    }

    if ($measure->getNumber() == 1 || !$measure->getTimeSignature()->isEqual($timeSignature)) {
      $flags |= 0x01;
      $flags |= 0x02;
    }

    if ($measure->isRepeatOpen()) {
      $flags |= 0x04;
    }

    if ($measure->getRepeatClose() > 0) {
      $flags |= 0x08;
    }

    if ($measure->getRepeatAlternative() > 0) {
      $flags |= 0x10;
    }

    if ($measure->hasMarker()) {
      $flags |= 0x20;
    }

    $this->writeUnsignedByte($flags);

    if (($flags & 0x01) != 0) {
      $this->writeByte($measure->getTimeSignature()->getNumerator());
    }

    if (($flags & 0x02) != 0) {
      $this->writeByte($measure->getTimeSignature()->getDenominator()->getValue());
    }

    if (($flags & 0x08) != 0) {
      $this->writeByte($measure->getRepeatClose() + 1);
    }

    if (($flags & 0x20) != 0) {
      $this->writeMarker($measure->getMarker());
    }

    if (($flags & 0x10) != 0) {
      $this->writeByte($measure->getRepeatAlternative());
    }

    if (($flags & 0x40) != 0) {
      $this->skipBytes(2);
    }

    if (($flags & 0x01) != 0) {
      $this->writeBytes( $this->makeEighthNoteBytes( $measure->getTimeSignature() ));
    }

    if (($flags & 0x10) == 0) {
      $this->writeByte(0);
    }

    if ($measure->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_NONE) {
      $this->writeByte(0);
    } elseif ($measure->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_EIGHTH) {
      $this->writeByte(1);
    } elseif ($measure->getTripletFeel() == MeasureHeader::TRIPLET_FEEL_SIXTEENTH) {
      $this->writeByte(2);
    }
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeMeasureHeaders(Song $song)
  {
    $timeSignature = new TimeSignature();

    if ($song->countMeasureHeaders() > 0) {
      foreach ($song->getMeasureHeaders() as $index => $header) {
        if ($index > 0) {
          $this->skipBytes(1);
        }
        $this->writeMeasureHeader($header, $timeSignature);
        $timeSignature->setNumerator($header->getTimeSignature()->getNumerator());
        $timeSignature->getDenominator()->setValue(
          $header->getTimeSignature()->getDenominator()->getValue()
        );
      }
    }
  }

  /**
   * @param \PhpTabs\Music\Song $song
   * @param \PhpTabs\Music\Tempo $tempo
   */
  private function writeMeasures(Song $song, Tempo $tempo)
  {
    foreach ($song->getMeasureHeaders() as $index =>$header) {

      foreach ($song->getTracks() as $track) {

        $measure = $track->getMeasure($index);
        $this->writeMeasure(
          $measure,
          $header->getTempo()->getValue() != $tempo->getValue()
        );
        $this->skipBytes(1);
      }

      $tempo->copyFrom($header->getTempo());
    }
  }

  /**
   * @param \PhpTabs\Music\Tempo $tempo
   */
  private function writeMixChange(Tempo $tempo)
  {
    for ($i = 0; $i < 23; $i++) {
      $this->writeByte(0xff);
    }

    $this->writeStringByteSizeOfInteger('');
    $this->writeInt($tempo !== null ? $tempo->getValue() : -1);

    if ($tempo !== null) {
      $this->skipBytes(1);
    }

    $this->writeByte(0x01);
    $this->writeByte(0xff);
  }

  /**
   * @param  \PhpTabs\Music\TimeSignature $timeSignature
   * @return array
   */
  private function makeEighthNoteBytes(TimeSignature $timeSignature)
  {
    $bytes = array(0, 0, 0, 0);

    if ($timeSignature->getDenominator()->getValue() <= Duration::EIGHTH) {
      $eighthsInDenominator = intval(Duration::EIGHTH / $timeSignature->getDenominator()->getValue());
      $total = ($eighthsInDenominator * $timeSignature->getNumerator());
      $byteValue = intval( $total / 4 );
      $missingValue = $total - (4 * $byteValue);

      for ($i = 0 ; $i < count($bytes); $i++) {
        $bytes[$i] = $byteValue;
      }

      if ($missingValue > 0) {
        $bytes[0] += $missingValue;
      }
    }

    return $bytes;
  }

  /**
   * @param  string $comments
   * @return array
   */
  private function toCommentLines($comments)
  {
    $lines = array();

    $line = $comments;

    while (strlen($line) > 127) {
      $subline = substr($line, 0, 127);
      $lines[] = $subline;
      $line = substr($line, 127);
    }

    $lines[] = $line;

    return $lines;
  }

  private function writeSetup()
  {
    $this->writeInt( 210 );
    $this->writeInt( 297 );
    $this->writeInt( 10 );
    $this->writeInt( 10 );
    $this->writeInt( 15 );
    $this->writeInt( 10 );
    $this->writeInt( 100 );

    $this->writeByte(255);
    $this->writeByte(1);

    for ($i = 0; $i < count($this->setUpLines); $i++) {
      $this->writeInt(strlen($this->setUpLines[$i]) + 1);
      $this->writeStringByte($this->setUpLines[$i], 0);
    }
  }

  /**
   * @param \PhpTabs\Music\Text $text
   */
  private function writeText(Text $text)
  {
    $this->writeStringByteSizeOfInteger($text->getValue());
  }

  /**
   * @param \PhpTabs\Music\Track $track
   */
  private function writeTrack(Track $track)
  {
    $channel = $this->getChannelRoute($track->getChannelId());

    $flags = 0;
    if ($track
          ->getSong()
          ->getChannelById($track->getChannelId())
          ->isPercussionChannel()
    ) {
      $flags |= 0x01;
    }

    $this->writeUnsignedByte($flags);
    $this->writeUnsignedByte(8 | $flags);
    $this->writeStringByte($track->getName(), 40);
    $this->writeInt(count($track->getStrings()));

    for ($i = 0; $i < 7; $i++) {
      $value = 0;

      if (count($track->getStrings()) > $i) {
        $string = $track->getStrings()[$i];
        $value = $string->getValue();
      }
      $this->writeInt($value);
    }

    $this->writeInt(1);
    $this->writeInt($channel->getChannel1() + 1);
    $this->writeInt($channel->getChannel2() + 1);
    $this->writeInt(24);
    $this->writeInt($track->getOffset());
    $this->writeColor($track->getColor());
    $this->writeBytes(
        array(
          67, 1, 0, 0,
          0, 0, 0, 0,
          0, 0, 0, 0,
          0, 100, 0, 0,
          0, 1, 2, 3,
          4, 5, 6, 7,
          8, 9, 10, -1,
          3, -1, -1, -1,
          -1, -1, -1, -1,
          -1, -1, -1, -1,
          -1, -1, -1, -1
        )
    );
  }

  /**
   * @param \PhpTabs\Music\Song $song
   */
  private function writeTracks(Song $song) 
  {
    foreach ($song->getTracks() as $track) {
      $this->writeTrack($track);
    }
  }
}
