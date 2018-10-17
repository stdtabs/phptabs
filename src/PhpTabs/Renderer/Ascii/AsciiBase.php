<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Renderer\Ascii;

use Exception;

class AsciiBase
{
  private $content = [];
  private $x = 0;
  private $y = 0;

  /**
   * Draw a bar segment
   */
  public function drawBarSegment()
  {
    $this->movePoint($this->getPosX() + 1, $this->getPosY());
    $this->append(AsciiRenderer::BAR_SEGMENT_CHR);
  }

  /**
   * Draw a note
   * 
   * @param string $fret
   */
  public function drawNote($fret)
  {
    $this->movePoint(
      $this->getPosX() + mb_strlen(strval($fret)),
      $this->getPosY()
    );

    $this->append(strval($fret));
  }

  /**
   * Draw a space
   */
  public function drawSpace()
  {
    $this->movePoint($this->getPosX() + 1, $this->getPosY());
    $this->append(" ");
  }

  /**
   * Draw a string line
   * 
   * @param string $string
   */
  public function drawStringLine($string)
  {
    $this->movePoint(0, $this->getPosY() + 1);
    $this->writeLn($string);
  }

  /**
   * Draw a string segment
   * 
   * @param int $count
   */
  public function drawStringSegments($count)
  {
    for ($i = 0; $i < $count; $i++) {
      $this->movePoint($this->getPosX() + $i + 1 + $count, $this->getPosY());
      $this->append(AsciiRenderer::STRING_SEGMENT_CHR);
    }
  }

  /**
   * Draw a tune segment
   * 
   * @param string $tune
   * @param int    $maxLength
   */
  public function drawTuneSegment($tune, $maxLength)
  {
    for ($i = mb_strlen($tune); $i < $maxLength; $i++) {
      $this->drawSpace();
    }

    $this->movePoint($this->getPosX() + mb_strlen($tune), $this->getPosY());

    $this->append($tune);
  }

  /**
   * Write a string in the current buffer
   * 
   * @param string $string
   */
  public function writeLn($string)
  {
    $length = mb_strlen($string);

    for ($i = 0; $i < $length; $i++) {
      $this->content[$this->getPosY()][$this->getPosX() + $i] = $string[$i];
    }
  }

  /**
   * Move buffer cursor
   * 
   * @param int $x
   * @param int $y
   */
  private function movePoint($x, $y)
  {
    $this->x = $x;
    $this->y = $y;
  }

  /**
   * Apped a new empty line
   */
  public function nextLine()
  {
    $this->movePoint(0, $this->getPosY() + 1);
    $this->writeLn("");
  }

  /**
   * Get X position
   * 
   * @return int
   */
  public function getPosX()
  {
    return $this->x;
  }

  /**
   * Get Y position
   * 
   * @return int
   */
  public function getPosY()
  {
    return $this->y;
  }

  /**
   * Write a string
   * 
   * @param string $data
   */
  public function append($data)
  {
    // Try to find a better X (min)
    $x = $this->getPosX();
    while ($x >= 0) {
      if (isset($this->content[$this->getPosY()][$x])) {
        break;
      }

      $x--;
    }

    $this->movePoint($x + 1, $this->getPosY());    

    for ($i = 0; $i < mb_strlen($data); $i++) {
      $this->write($this->getPosX() + $i, $this->getPosY() , $data{$i});
    }
  }

  /**
   * Write a character into the buffer
   * 
   * @param int    $x
   * @param int    $y
   * @param string $char
   */
  public function write($x, $y, $char)
  {
    // Slot must be empty, throw exception on overwrite attempts
    if (isset($this->content[$y], $this->content[$y][$x])) {
      throw new Exception (
        sprintf(
          "Trying to write a written position. x=%s, y=%s, char=%s. Value=%s",
          $x,
          $y,
          $char,
          $this->content[$y][$x]
        )
      );
    }

    $this->content[$y][$x] = $char;
  }

  /**
   * Return all buffered data as a string
   * 
   * @return string
   */
  public function output()
  {
    $maxLines = max(array_keys($this->content));
    $maxCols  = $this->findMaxValue();

    $content = '';

    for ($line = 0; $line <= $maxLines; $line++) {
      for ($col = 0; $col <= $maxCols; $col++) {
        if (isset($this->content[$line], $this->content[$line][$col])) {
          $content .= $this->content[$line][$col];
        }
      }

      $content .= PHP_EOL;
    }

    return $content;
  }

  /**
   * Find maximum column index
   * 
   * @return int
   */
  private function findMaxValue()
  {
    return array_reduce($this->content, function ($carry, $item) {
      return max($carry, max(array_keys($item)));
    }, 0);
  }
}
