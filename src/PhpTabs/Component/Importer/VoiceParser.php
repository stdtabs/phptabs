<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Importer;

use PhpTabs\Music\Voice;

class VoiceParser extends ParserBase
{
  protected $required = ['duration', 'index', 'empty', 'direction', 'notes'];

  /**
   * Parse a voice array
   * 
   * @param  int   $index
   * @param  array $data
   */
  public function __construct($index, array $data)
  {
    $this->checkKeys($data, $this->required);

    $voice = new Voice($index);

    $voice->setDuration(
      $this->parseDuration($data['duration'])
    );

    $voice->setDirection($data['direction']);

    foreach ($data['notes'] as $note) {
      $this->checkKeys($note, 'note');
      $voice->addNote(
        $this->parseNote($note['note'])
      );
    }

    $voice->setEmpty($data['empty']);

    $this->item = $voice;
  }
}
