<?php

/*
 * This file is part of the PhpTabs package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/stdtabs/phptabs/blob/master/LICENSE>.
 */

namespace PhpTabs\Component\Serializer;

/**
 * Text serializer
 */
class Text extends SerializerBase
{
    const INDENT_STEP = 2;
    const INDENT_CHAR = ' ';

    /**
     * @var int
     */
    protected $depth;

    /**
     * @var string
     */
    protected $content;

    /**
     * Serialize a document
     */
    public function serialize(array $document): string
    {
        $this->depth = 0;
        $this->appendNodes($document);

        return $this->content;
    }

    /**
     * Append a node
     */
    protected function appendNode(string $index, array $element): void
    {
        $this->content .= sprintf('%s%s:%s', $this->indent(), $index, PHP_EOL);
        $this->depth++;
        $this->appendNodes($element);
        $this->depth--;
    }

    /**
     * @param string $index
     * @param bool|int|float|string $element
     */
    protected function appendText(string $index, $element): void
    {
        $this->content .= sprintf('%s%s:', $this->indent(), $index);

        if ($element === false) {
            $this->content .= 'false';
        } elseif ($element === true) {
            $this->content .= 'true';
        } elseif (strpos($element, PHP_EOL) !== false) {
            $this->writeMultilineString($element);
        } elseif (is_string($element)) {
            $this->content .= sprintf('"%s"', $element);
        } elseif (is_numeric($element)) {
            $this->content .= $element;
        }

        $this->content .= PHP_EOL;
    }

    /**
     * Write a multiline string
     */
    protected function writeMultilineString(string $value): void
    {
        $this->depth++;

        $strings = explode(PHP_EOL, $value);

        foreach ($strings as $string) {
            $this->content .= sprintf('%s%s%s', PHP_EOL, $this->indent(), $string);
        }

        $this->depth--;
    }

    /**
     * Get a normalized indent
     */
    protected function indent(): string
    {
        return str_repeat(Text::INDENT_CHAR, $this->depth * Text::INDENT_STEP);
    }
}
