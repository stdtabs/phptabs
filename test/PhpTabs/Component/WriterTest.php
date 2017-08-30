<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabsTest\Component;

use PHPUnit_Framework_TestCase;
use PhpTabs\Component\Tablature;
use PhpTabs\Component\Writer;

class WriterTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException Exception
   */
  public function testNotAllowedFormatException()
  {
    (new Writer( new Tablature() ))->save('xxx');
  }

  /**
   * @expectedException Exception
   */
  public function testEmptySongDefaultException()
  {
    (new Writer( new Tablature() ))->save();
  }

  /**
   * @expectedException Exception
   * gp3
   */
  public function testEmptySongGp3Exception()
  {
    (new Writer( new Tablature() ))->build('gp3');
  }

  /**
   * @expectedException Exception
   * gp4
   */
  public function testEmptySongGp4Exception()
  {
    (new Writer( new Tablature() ))->build('gp4');
  }
}
