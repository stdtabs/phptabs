<?php

namespace PhpTabs\Component\Serializer;

/**
 * Text serializer
 */
class Text
{
  const INDENT_STEP = 2;
  const INDENT_CHAR = ' ';

  /**
   * @var integer $depth
   * @var string $content
   */
  private $depth;
  private $content;

  /**
   * Serializes a document
   * 
   * @param array $document
   * @return string
   */
  public function serialize(array $document)
  {
    $this->depth = 0;
    $this->appendNodes($document);

    return $this->content;
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
      // Element
      else if(is_array($node) && !is_int($index))
      {
        $this->appendNode($index, $node);
      }
      // SubElement
      else if(!is_array($node))
      {
        $this->appendText($index, $node);
      }
    }
  }

  private function appendNode($index, array $element)
  {
    $this->content .= sprintf('%s%s:%s', $this->indent(), $index, PHP_EOL);
    $this->depth++;
    $this->appendNodes($element);
    $this->depth--;
  }

  private function appendText($index, $value)
  {
    $this->content .= sprintf('%s%s:', $this->indent(), $index);

    if($value === false)
    {
      $this->content .= 'false';
    }
    else if($value === true)
    {
      $this->content .= 'true';
    }
    else if(strpos($value, PHP_EOL) !== false)
    {
      $this->writeMultilineString($value);
    }
    else
    {
      $this->content .= $value;
    }

    $this->content .= PHP_EOL;
  }

  private function writeMultilineString($value)
  {
    $this->depth++;

    $strings = explode(PHP_EOL, $value);

    foreach($strings as $string)
    {
      $this->content .= sprintf('%s%s%s', PHP_EOL, $this->indent(), $string);
    }

    $this->depth--;
  }

  private function indent()
  {
    return str_repeat(Text::INDENT_CHAR, $this->depth * Text::INDENT_STEP);
  }
}
