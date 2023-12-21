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

use DOMDocument;
use PhpTabs\PhpTabs;

/**
 * Helpers for testing exports of a simple tablature
 * Guitar Pro 3, 4, 5 & MIDI
 */
abstract class ExporterHelper extends XmlTestCaseHelper
{
    private static $tablature;
    private static $plainText;
    private static $yamlText;
    private static $xmlDoc;

    /**
     * Prepare some read only data
     */
    public static function setUpBeforeClass() : void
    {
        self::$tablature = new PhpTabs(
            PHPTABS_TEST_BASEDIR
            . '/samples/'
            . static::getFilename()
        );

        // Text
        self::$plainText = self::$tablature->toText();
        // YAML
        self::$yamlText = self::$tablature->toYaml();

        // XML
        $xmlString = self::$tablature->toXml();
        $document = new DOMDocument();
        $document->loadXML($xmlString);
        self::$xmlDoc = $document;
    }

    public function getXmlDocument()
    {
        return self::$xmlDoc;
    }

    /**
     * Exporter XML
     */
    public function testExporterXml()
    {
        // expected, xpath, message
        $tests = [
            [1, 'count(/song)', 'Incorrect number of Song elements.'],

            // Meta attributes
            [ self::$tablature->getName()
            , 'string(/song/name)'
            , 'Incorrect or missing Name element.'],
            [ self::$tablature->getArtist()
            , 'string(/song/artist)'
            , 'Incorrect or missing Artist element.'],
            [ self::$tablature->getAlbum()
            , 'string(/song/album)'
            , 'Incorrect or missing Album element.'],
            [ self::$tablature->getAuthor()
            , 'string(/song/author)'
            , 'Incorrect or missing Author element.'],
            [ self::$tablature->getCopyright()
            , 'string(/song/copyright)'
            , 'Incorrect or missing Copyright element.'],
            [ self::$tablature->getWriter()
            , 'string(/song/writer)'
            , 'Incorrect or missing Writer element.'],
            [ self::$tablature->getComments()
            , 'string(/song/comments)'
            , 'Incorrect or missing Comments element.'],
            [ self::$tablature->getDate()
            , 'string(/song/date)'
            , 'Incorrect or missing Date element.'],
            [ self::$tablature->getTranscriber()
            , 'string(/song/transcriber)'
            , 'Incorrect or missing Transcriber element.'],

            // Tracks
            [ self::$tablature->countTracks()
            , 'count(/song/tracks/track)'
            , 'Incorrect number of Track elements.'],
            [ 0
            , 'count(/song/tracks/track[42])'
            , 'Track element should NOT exist.'],
            [ 1
            , 'count(/song/tracks/track[1])'
            , 'Track element should exist.'],

            // Channels
            [ self::$tablature->countChannels()
            , 'count(/song/channels/channel)'
            , 'Incorrect number of Channel elements.'],
            [ 0
            , 'count(/song/channels/channel[42])'
            , 'Channel element should NOT exist.'],
            [ 1
            , 'count(/song/channels/channel[1])'
            , 'Channel element should exist.'],

            // MeasureHeaders
            [ self::$tablature->countMeasureHeaders()
            , 'count(/song/measureHeaders/header)'
            , 'Incorrect number of MeasureHeader elements.'],
            [ 0
            , 'count(/song/measureHeaders/header[42])'
            , 'MeasureHeader element should NOT exist.'],
            [ 1
            , 'count(/song/measureHeaders/header[1])'
            , 'MeasureHeader element should exist.']
        ];

        foreach ($tests as $test) {
            self::assertXpathMatch($test[0], $test[1], $test[2]);
        }
    }

    /**
     * Text serialization
     *
     * @dataProvider getTextScenarios
     */
    public function testExporterText($text)
    {
        $pattern = sprintf('/%s/', $text);

        // PHPUnit >= 9
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            self::assertMatchesRegularExpression($pattern, self::$plainText);
        // PHPUnit < 9
        } else {
            self::assertRegExp($pattern, self::$plainText);
        }
    }

    /**
     * Text serialization
     *
     * @dataProvider getYamlScenarios
     */
    public function testExporterYaml($text)
    {
        $pattern = sprintf('/%s/', $text);

        // PHPUnit >= 9
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            self::assertMatchesRegularExpression($pattern, self::$plainText);
        // PHPUnit < 9
        } else {
            self::assertRegExp($pattern, self::$plainText);
        }
    }

    public static function getYamlScenarios()
    {
        return static::getTextScenarios();
    }
}
