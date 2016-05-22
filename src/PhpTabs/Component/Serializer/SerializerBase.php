<?php

namespace PhpTabs\Component\Serializer;

abstract class SerializerBase
{
  protected function appendNodes(array $nodes)
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
}
