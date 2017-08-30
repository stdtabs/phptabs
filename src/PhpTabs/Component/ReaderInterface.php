<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component;

/**
 * Interface for the bridge readers
 */
interface ReaderInterface
{
  /**
   * @return \PhpTabs\Component\Tablature built from file read
   */
  public function getTablature();
}
