<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component\Exporter;

/**
 * Tests with a simple Midi file
 */
class MidiExporterTest extends ExporterHelper
{
    protected static function getFilename()
    {
        return 'testSimpleMidi.mid';
    }

    /**
     * Provider for text scenarios
     */
    public static function getTextScenarios()
    {
        return [
        ['song:'],
        ['name:'],
        ['artist:'],
        ['album:'],
        ['author:'],
        ['copyright:'],
        ['writer:'],
        ['comments:'],
        ['channels:'],
        ['channel:'],
        ['channelId:'],
        ['bank:'],
        ['program:'],
        ['volume:'],
        ['balance:'],
        ['chorus:'],
        ['reverb:'],
        ['phaser:'],
        ['tremolo:'],
        ['parameters:'],
        ['measureHeaders:'],
        ['number:'],
        ['start:'],
        ['timeSignature:'],
        ['numerator:'],
        ['denominator:'],
        ['dotted:'],
        ['doubleDotted:'],
        ['divisionType:'],
        ['enters:'],
        ['times:'],
        ['tempo:'],
        ['marker:'],
        ['repeatOpen:'],
        ['repeatAlternative:'],
        ['repeatClose:'],
        ['tripletFeel:'],
        ['tracks:'],
        ['offset:'],
        ['solo:'],
        ['mute:'],
        ['color:'],
        ['R:'],
        ['G:'],
        ['B:'],
        ['lyrics:'],
        ['from:'],
        ['measures:'],
        ['measure:'],
        ['clef:'],
        ['keySignature:'],
        ['header:'],
        ['beats:'],
        ['chord:'],
        ['text:'],
        ['voices:'],
        ['duration:'],
        ['index:'],
        ['empty:'],
        ['direction:'],
        ['notes:'],
        ['stroke:'],
        ['velocity:'],
        ['string:'],
        ['tiedNote:'],
        ['effect:'],
        ['bend:'],
        ['tremoloBar:'],
        ['harmonic:'],
        ['grace:'],
        ['trill:'],
        ['tremoloPicking:'],
        ['vibrato:'],
        ['deadNote:'],
        ['slide:'],
        ['hammer:'],
        ['ghostNote:'],
        ['accentuatedNote:'],
        ['heavyAccentuatedNote:'],
        ['palmMute:'],
        ['staccato:'],
        ['tapping:'],
        ['slapping:'],
        ['popping:'],
        ['fadeIn:'],
        ['letRing:'],
        ['points:'],
        ['position:'],
        ['strings:']
        ];
    }
}
