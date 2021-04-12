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

    /**
     * @param array $element A node
     */
    protected function appendNode(string $index, array $element): void
    {
        $this->writer->startElement($index);
        $this->appendNodes($element);
        $this->writer->endElement();
    }

    /**
     * @param string $index
     * @param int|bool|float|string $element
     */
    protected function appendText(string $index, $element): void
    {
        $this->writer->startElement($index);

        if ($element === false) {
            $element = 'false';
        } elseif ($element === true) {
            $element = 'true';
        }

        if (is_scalar($element)) {
            $this->writer->text($element);
        }

        $this->writer->endElement();
    }
}
