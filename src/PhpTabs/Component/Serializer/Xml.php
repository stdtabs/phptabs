<?php

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
   * @param array $document
   * @return string
   */
  public function serialize(array $document)
  {
    $this->writer->flush();
    $this->writer->startDocument('1.0', 'UTF-8');
    $this->appendNodes($document);

    return $this->writer->outputMemory();
  }

  protected function appendNode($index, array $node)
  {
    $this->writer->startElement($index);
    $this->appendNodes($node);
    $this->writer->endElement(); 
  }

  protected function appendText($index, $value)
  {
    $this->writer->startElement($index);

    if($value === false)
    {
      $this->writer->text('false');
    }
    else if($value === true)
    {
      $this->writer->text('true');
    }
    else if(is_numeric($value) || is_string($value))
    {
      $this->writer->text($value);
    }

    $this->writer->endElement(); 
  }
}
