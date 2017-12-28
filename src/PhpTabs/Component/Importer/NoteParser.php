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

use PhpTabs\Music\Note;

class NoteParser extends ParserBase
{
  protected $required = ['value', 'velocity', 'string', 'tiedNote', 'effect'];

  /**
   * Parse a note array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $note = new Note();
    $note->setValue($data['value']);
    $note->setVelocity($data['velocity']);
    $note->setString($data['string']);
    $note->setTiedNote($data['tiedNote']);

    $note->setEffect(
      $this->parseNoteEffect($data['effect'])
    );

    $this->item = $note;
  }
}
