<?php

declare(strict_types=1);

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
 * Yaml serializer
 */
final class Yaml extends SerializerBase
{
    public const INDENT_STEP = 2;
    public const INDENT_CHAR = ' ';

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

    protected function appendNode(string $index, array $element): void
    {
        $this->content .= sprintf(
            '%s%s:%s',
            $this->indent(),
            $index,
            PHP_EOL
        );

        $this->depth++;
        $this->appendNodes($element);
        $this->depth--;
    }

    /**
     * @param string|int|bool $element
     */
    protected function appendText(string $index, $element): void
    {
        $this->content .= sprintf(
            '%s%s: %s%s',
            $this->indent(),
            $index,
            $this->formatValue($element),
            PHP_EOL
        );
    }

    /**
     * Format a string value
     *
     * @param  string|int|bool $value
     */
    private function formatValue($value): string
    {
        switch (true) {
            case $value === false:
                return 'false';
            case $value === true:
                return 'true';
            case is_string($value):
                return sprintf('"%s"', $value);
            case is_null($value):
                return sprintf('%s', $value);
            case is_numeric($value):
                return "{$value}";
            case strpos($value, PHP_EOL) !== false:
                return $this->getMultilineString($value);
        }

        return '';
    }

    private function getMultilineString(string $value): string
    {
        $this->depth++;

        $strings = explode(PHP_EOL, $value);

        $content = '|';

        foreach ($strings as $string) {
            $content .= sprintf(
                '%s%s%s',
                PHP_EOL,
                $this->indent(),
                $string
            );
        }

        $this->depth--;

        return $content;
    }

    protected function indent(): string
    {
        return str_repeat(Text::INDENT_CHAR, $this->depth * Text::INDENT_STEP);
    }
}
