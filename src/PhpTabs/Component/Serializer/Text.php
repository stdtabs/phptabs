<?php

namespace PhpTabs\Component\Serializer;

/**
 * Text serializer
 */
class Text extends SerializerBase
{
  const INDENT_STEP = 2;
  const INDENT_CHAR = ' ';

  /**
   * @var integer $depth
   * @var string $content
   */
  protected $depth;
  protected $content;

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

  protected function appendNode($index, array $element)
  {
    $this->content .= sprintf('%s%s:%s', $this->indent(), $index, PHP_EOL);
    $this->depth++;
    $this->appendNodes($element);
    $this->depth--;
  }

  protected function appendText($index, $value)
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
    else if(is_string($value))
    {
      $this->content .= sprintf('"%s"', $value);
    }
    else if(is_numeric($value))
    {
      $this->content .= $value;
    }

    $this->content .= PHP_EOL;
  }

  protected function writeMultilineString($value)
  {
    $this->depth++;

    $strings = explode(PHP_EOL, $value);

    foreach($strings as $string)
    {
      $this->content .= sprintf('%s%s%s', PHP_EOL, $this->indent(), $string);
    }

    $this->depth--;
  }

  protected function indent()
  {
    return str_repeat(Text::INDENT_CHAR, $this->depth * Text::INDENT_STEP);
  }
}
