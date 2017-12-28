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

use PhpTabs\Music\Channel;

class ChannelParser extends ParserBase
{
  protected $required = [
      'channelId',
      'name',
      'bank',
      'program',
      'volume',
      'balance',
      'chorus',
      'reverb',
      'phaser',
      'tremolo',
      'parameters'
  ];

  /**
   * Parse a channel array
   * 
   * @param  array $data
   */
  public function __construct(array $data)
  {
    $this->checkKeys($data, $this->required);

    $channel = new Channel();
    $channel->setChannelId($data['channelId']);
    $channel->setName($data['name']);
    $channel->setBank($data['bank']);
    $channel->setProgram($data['program']);
    $channel->setVolume($data['volume']);
    $channel->setBalance($data['balance']);
    $channel->setChorus($data['chorus']);
    $channel->setReverb($data['reverb']);
    $channel->setPhaser($data['phaser']);
    $channel->setTremolo($data['tremolo']);

    foreach ($data['parameters'] as $parameter) {
      $channel->addParameter(
        $this->parseChannelParameter($parameter)
      );
    }

    $this->item = $channel;
  }
}
