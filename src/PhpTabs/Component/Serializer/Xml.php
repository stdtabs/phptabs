<?php

namespace PhpTabs\Component\Serializer;

use XMLWriter;

/**
 * XML serializer
 */
class Xml
{
  private $writer;

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

  private function appendNodes(array $nodes)
  {
    foreach($nodes as $index => $node)
    {
      // List
      if(is_array($node) && is_int($index))
      {
        $this->appendNodes($node);
      }
      // Node
      else if(is_array($node) && !is_int($index))
      {
        $this->appendNode($index, $node);
      }
      // Text
      else if(!is_array($node))
      {
        $this->appendText($index, $node);
      }
    }
  }

  private function appendNode($index, array $node)
  {
    $this->writer->startElement($index);
    $this->appendNodes($node);
    $this->writer->endElement(); 
  }

  private function appendText($index, $value)
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
    else
    {
      $this->writer->text($value);
    }

    $this->writer->endElement(); 
  }
}
