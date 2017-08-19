<?php

namespace PhpTabsTest\Renderer;

use Exception;
use PHPUnit_Framework_TestCase;
use PhpTabs\PhpTabs;

/**
 * Tests with a simple tablature
 * Render simple tabs, VexTab formatted
 */
class VexTabRendererTest extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->filename = 'testSimpleTab.gp3';
    $this->tablature = new PhpTabs(PHPTABS_TEST_BASEDIR . '/samples/' . $this->filename);
  }

  /**
   * Renderer
   */
  public function testRenderer()
  {
    $options = [
      'notation'            => 'true',
      'tablature'           => 'true',
      'measures_per_stave'  => 2
    ];

    $renderer = $this->tablature
      ->getRenderer('vextab')
      ->setOptions($options);

    # have a good type
    $this->assertInstanceOf(
      'PhpTabs\\Component\\RendererInterface',
      $renderer
    );

    # Render 2 tracks
    $this->assertEquals(
      'options tempo=66 width=1024 scale=0.8 space=16

tabstave
	notation=true
	tablature=true
	clef=treble
	time=12/8

notes =|: :w ## :qd ## :16 t11/3 X/2 |:qd 10v/1 :8 T10/1 13/2 12b14/3 :16 12/3 p10/3 12/4 ^3^ :8d s12v/4 :16 10/3 12/4 p10/4 ## :8d 12v/4 

tabstave
	notation=true
	tablature=true
	clef=treble
notes :16 10/3 12/3 :8 12b14/3 10v/2 :q T10/2 :8 10/2 10/2 12/2 13/2 14/2 15/2 t10/2 |:8 13b15/2 :q 12/2 :qd 10/2 :8 10/2 ## 12b14/1 12/1 ## ## =:|',
      $renderer->render(0)
    );

    $this->assertEquals(
      'options tempo=66 width=1024 scale=0.8 space=16

tabstave
	notation=true
	tablature=true
	clef=treble
	time=12/8

notes =|: :q 5/2 5/2 5/2 5/2 5/2 5/2 |:q 5/2 5/2 (3/1.5/2.5/3.5/4.3/5.3/6) 5/2 5/2 5/2 

tabstave
	notation=true
	tablature=true
	clef=treble
notes :q 5/2 5d/2 5/2 5b7b9v/2 5/2 5/2 |:q 5/2 :8 5v/2 ## :q 5/2 5/2 5/2 5/2 =:|',
      $renderer->render(1)
    );
  }

  /**
   * @expectedException \Exception
   */
  public function testBadFormatException()
  {
    $this->tablature->getRenderer('Not a valid format');
  }

  public function tearDown()
  {
    unset($this->tablature);
  }
}