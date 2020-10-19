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
    public function drawBarSegment(): void
    {
        $this->movePoint($this->getPosX() + 1, $this->getPosY());
        $this->append(AsciiRenderer::BAR_SEGMENT_CHR);
    }

    /**
     * Draw a note
     */
    public function drawNote(string $fret): void
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
    public function drawSpace(): void
    {
        $this->movePoint($this->getPosX() + 1, $this->getPosY());
        $this->append(" ");
    }

    /**
     * Draw a string line
     */
    public function drawStringLine(string $string): void
    {
        $this->movePoint(0, $this->getPosY() + 1);
        $this->writeLn($string);
    }

    /**
     * Draw a string segment
     */
    public function drawStringSegments(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->movePoint($this->getPosX() + $i + 1 + $count, $this->getPosY());
            $this->append(AsciiRenderer::STRING_SEGMENT_CHR);
        }
    }

    /**
     * Draw a tune segment
     */
    public function drawTuneSegment(string $tune, int $maxLength): void
    {
        for ($i = mb_strlen($tune); $i < $maxLength; $i++) {
            $this->drawSpace();
        }

        $this->movePoint($this->getPosX() + mb_strlen($tune), $this->getPosY());

        $this->append($tune);
    }

    /**
     * Write a string in the current buffer
     */
    public function writeLn(string $string): void
    {
        $length = mb_strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $this->content[$this->getPosY()][$this->getPosX() + $i] = $string[$i];
        }
    }

    /**
     * Move buffer cursor
     */
    private function movePoint(int $x, int $y): void
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Append a new empty line
     */
    public function nextLine(): void
    {
        $this->movePoint(0, $this->getPosY() + 1);
        $this->writeLn("");
    }

    /**
     * Get X position
     */
    public function getPosX(): int
    {
        return $this->x;
    }

    /**
     * Get Y position
     */
    public function getPosY(): int
    {
        return $this->y;
    }

     /**
      * Write a string
      */
    public function append(string $data): void
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
            $this->write(
                $this->getPosX() + $i,
                $this->getPosY(),
                mb_substr($data, $i, 1)
            );
        }
    }

    /**
     * Write a character into the buffer
     */
    public function write(int $x, int $y, string $char): void
    {
        // Slot must be empty, throw exception on overwrite attempts
        if (isset($this->content[$y], $this->content[$y][$x])) {
            throw new Exception(
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
     */
    public function output(): string
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
     */
    private function findMaxValue(): int
    {
        return array_reduce($this->content, function ($carry, $item) {
            return max($carry, max(array_keys($item)));
        }, 0);
    }
}
