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

use XMLWriter;

/**
 * XML serializer
 */
class Xml extends SerializerBase
{
    protected $writer;

    public function __construct()
    {
        $this->writer = new XMLWriter;
        $this->writer->openMemory();
        $this->writer->setIndent(true);
    }

    /**
     * Serialize a document
     */
    public function serialize(array $document): string
    {
        $this->writer->flush();
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->appendNodes($document);

        return $this->writer->outputMemory();
    }

    protected function appendNode(string $index, array $node): void
    {
        $this->writer->startElement($index);
        $this->appendNodes($node);
        $this->writer->endElement();
    }

    /**
     * @param int $index
     * @param int|bool|float|string $value
     */
    protected function appendText(string $index, $value): void
    {
        $this->writer->startElement($index);

        if ($value === false) {
            $value = 'false';
        } elseif ($value === true) {
            $value = 'true';
        }

        if (is_scalar($value)) {
            $this->writer->text($value);
        }

        $this->writer->endElement();
    }
}
