<?php

namespace PhpTabsTest\Component\Dumper;

use DOMDocument;
use PhpTabs\PhpTabs;

/**
 * Tests with a simple tablature
 * Guitar Pro 4
 */
class GuitarPro4DumperTest extends XmlTestCaseHelper
{
  public function setUp()
  {
    $this->filename = 'testSimpleTab.gp4';
    $this->tablature = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/' . $this->filename);
  }

  public function getXmlDocument()
  {
    $xmlString = $this->tablature->dump('xml');

    $document = new DOMDocument();    
    $document->loadXML($xmlString);

    return $document;
  }

  /**
   * Dumper XML
   */
  public function testDumperXml()
  {
    // expected, xpath, message
    $tests = array(
      [1, 'count(/song)', 'Incorrect number of Song elements.'],
      
      # Meta attributes
      [ $this->tablature->getName()
        , 'string(/song/name)'
        , 'Incorrect or missing Name element.'],
      [ $this->tablature->getArtist()
        , 'string(/song/artist)'
        , 'Incorrect or missing Artist element.'],
      [ $this->tablature->getAlbum()
        , 'string(/song/album)'
        , 'Incorrect or missing Album element.'],
      [ $this->tablature->getAuthor()
        , 'string(/song/author)'
        , 'Incorrect or missing Author element.'],
      [ $this->tablature->getCopyright()
        , 'string(/song/copyright)'
        , 'Incorrect or missing Copyright element.'],
      [ $this->tablature->getWriter()
        , 'string(/song/writer)'
        , 'Incorrect or missing Writer element.'],
      [ $this->tablature->getComments()
        , 'string(/song/comments)'
        , 'Incorrect or missing Comments element.'],
      [ $this->tablature->getDate()         #Not supported by Guitar Pro 4
        , 'string(/song/date)'
        , 'Incorrect or missing Date element.'],
      [ $this->tablature->getTranscriber()  #Not supported by Guitar Pro 4
        , 'string(/song/transcriber)'
        , 'Incorrect or missing Transcriber element.'],
        
      # Tracks
      [ $this->tablature->countTracks()
        , 'count(/song/tracks/track)'
        , 'Incorrect number of Track elements.'],
      [ 0
        , 'count(/song/tracks/track[42])'
        , 'Track element should NOT exist.'],
      [ 1
        , 'count(/song/tracks/track[1])'
        , 'Track element should exist.'],

      # Channels
      [ $this->tablature->countChannels()
        , 'count(/song/channels/channel)'
        , 'Incorrect number of Channel elements.'],
      [ 0
        , 'count(/song/channels/channel[42])'
        , 'Channel element should NOT exist.'],
      [ 1
        , 'count(/song/channels/channel[1])'
        , 'Channel element should exist.'],

      # MeasureHeaders
      [ $this->tablature->countMeasureHeaders()
        , 'count(/song/measureHeaders/header)'
        , 'Incorrect number of MeasureHeader elements.'],
      [ 0
        , 'count(/song/measureHeaders/header[42])'
        , 'MeasureHeader element should NOT exist.'],
      [ 1
        , 'count(/song/measureHeaders/header[1])'
        , 'MeasureHeader element should exist.']
    );

    foreach($tests as $test)
    {
      $this->assertXpathMatch($test[0], $test[1], $test[2]);
    }
  }

  /**
   * Text serialization
   */
  public function testDumperText()
  {
    $tests = array(
      'song:',
      'name:',
      'artist:',
      'album:',
      'author:',
      'copyright:',
      'writer:',
      'comments:',
      'channels:',
      'channelId:',
      'bank:',
      'program:',
      'volume:',
      'balance:',
      'chorus:',
      'reverb:',
      'phaser:',
      'tremolo:',
      'parameters:',
      'key:',
      'value:',
      'measureHeaders:',
      'number:',
      'start:',
      'timeSignature:',
      'numerator:',
      'denominator:',
      'dotted:',
      'doubleDotted:',
      'divisionType:',
      'enters:',
      'times:',
      'tempo:',
      'marker:',
      'repeatOpen:',
      'repeatAlternative:',
      'repeatClose:',
      'tripletFeel:',
      'tracks:',
      'track:',
      'offset:',
      'solo:',
      'mute:',
      'color:',
      'R:',
      'G:',
      'B:',
      'lyrics:',
      'from:',
      'measures:',
      'measure:',
      'clef:',
      'keySignature:',
      'header:',
      'beats:',
      'chord:',
      'text:',
      'voices:',
      'duration:',
      'index:',
      'empty:',
      'direction:',
      'notes:',
      'stroke:',
      'velocity:',
      'string:',
      'tiedNote:',
      'effect:',
      'bend:',
      'tremoloBar:',
      'harmonic:',
      'grace:',
      'trill:',
      'tremoloPicking:',
      'vibrato:',
      'deadNote:',
      'slide:',
      'hammer:',
      'ghostNote:',
      'accentuatedNote:',
      'heavyAccentuatedNote:',
      'palmMute:',
      'staccato:',
      'tapping:',
      'slapping:',
      'popping:',
      'fadeIn:',
      'letRing:',
      'points:',
      'position:',
      'strings:'
    );

    $plainText = $this->tablature->dump('text');

    foreach($tests as $test)
    {
      $pattern = sprintf('/%s/', $test);
      $this->assertRegexp($pattern, $plainText);
    }
  }

  public function tearDown()
  {
    unset($this->filename);
    unset($this->tablature);
  }
}
