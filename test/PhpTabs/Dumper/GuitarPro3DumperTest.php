<?php

namespace PhpTabsTest\Dumper;

use DOMDocument;
use PhpTabs\PhpTabs;

/**
 * Tests with a simple tablature
 * Guitar Pro 3
 */
class GuitarPro3DumperTest extends XmlTestCaseHelper
{
  public function setUp()
  {
    $this->filename = 'testSimpleTab.gp3';
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
      [ $this->tablature->getDate()               #Not supported by Guitar Pro 3
        , 'string(/song/date)'
        , 'Incorrect or missing Date element.'],
      [ $this->tablature->getTranscriber()        #Not supported by Guitar Pro 3
        , 'string(/song/transcriber)'
        , 'Incorrect or missing Transcriber element.'],
        
      # Tracks
      [ $this->tablature->countTracks()
        , 'count(/song/tracks/number)'
        , 'Incorrect number of Track elements.'],
      [ 0
        , 'count(/song/tracks/number[42])'
        , 'Track element should NOT exist.'],
      [ 1
        , 'string(/song/tracks/number[1])'
        , 'Track element should exist.'],

      # Channels
      [ $this->tablature->countChannels()
        , 'count(/song/channels/channelId)'
        , 'Incorrect number of Channel elements.'],
      [ 0
        , 'count(/song/channels/channelId[42])'
        , 'Channel element should NOT exist.'],
      [ 1
        , 'string(/song/channels/channelId[1])'
        , 'Channel element should exist.'],

      # MeasureHeaders
      [ $this->tablature->countMeasureHeaders()
        , 'count(/song/measureHeaders/number)'
        , 'Incorrect number of MeasureHeader elements.'],
      [ 0
        , 'count(/song/measureHeaders/number[42])'
        , 'MeasureHeader element should NOT exist.'],
      [ 1
        , 'string(/song/measureHeaders/number[1])'
        , 'MeasureHeader element should exist.']
    );

    foreach($tests as $test)
    {
      $this->assertXpathMatch($test[0], $test[1], $test[2]);
    }
  }

  /**
   * Test Text
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
