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
   * Serializes a document
   * 
   * @param  array $document
   * @return string
   */
  public function serialize(array $document)
  {
    $this->writer->flush();
    $this->writer->startDocument('1.0', 'UTF-8');
    $this->appendNodes($document);

    return $this->writer->outputMemory();
  }

  /**
   * @param int   $index
   * @param array $node
   */
  protected function appendNode($index, array $node)
  {
    $this->writer->startElement($index);
    $this->appendNodes($node);
    $this->writer->endElement(); 
  }

  /**
   * @param int $index
   * @param int $value
   */
  protected function appendText($index, $value)
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
