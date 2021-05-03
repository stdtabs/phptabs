<?php

declare(strict_types=1);

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Writer\Midi;

use PhpTabs\Music\Note;

final class MidiNoteHelper
{
    /**
     * @var \PhpTabs\Writer\Midi\MidiMeasureHelper
     */
    private $measure;

    /**
     * @var \PhpTabs\Music\Note
     */
    private $note;

    public function __construct(MidiMeasureHelper $measure, Note $note)
    {
        $this->measure = $measure;
        $this->note = $note;
    }

    public function getMeasure(): MidiMeasureHelper
    {
        return $this->measure;
    }

    public function getNote(): Note
    {
        return $this->note;
    }
}
