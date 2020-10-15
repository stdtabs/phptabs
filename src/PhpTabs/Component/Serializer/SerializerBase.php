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

abstract class SerializerBase
{
    /**
     * Walk a node list and append them
     */
    protected function appendNodes(array $nodes): void
    {
        array_walk(
            $nodes,
            function ($node, $index) {
                // List
                if (is_array($node) && is_int($index)) {

                    $this->appendNodes($node);

                // Node
                } elseif (is_array($node) && !is_int($index)) {

                    $this->appendNode($index, $node);

                // Text
                } elseif (!is_array($node)) {

                    $this->appendText($index, $node);
                }
            }
        );
    }
}
