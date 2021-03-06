.. _parse.strings:

==================
Parse from strings
==================

Sometimes, you may need to parse a song from a string (binary or not). 

IOFactory
=========

It's made with the ``fromString()`` method.

After a read operation, a `PhpTabs` containing the entire song is
returned.

.. code-block:: php

    use PhpTabs\IOFactory;

    $content = file_get_contents('my-file.gp5');

    $song = IOFactory::fromString($content, 'gp5');

    echo $song->getName();

The file format is given as second parameter (gp3, gp4, gp5, mid, midi,
json, ser).


`IOFactory` offers some other shortcuts to force a parser.

.. code-block:: php

    use PhpTabs\IOFactory;

    // Parse a JSON content
    $song = IOFactory::fromJson($content);

    // Parse a PHP serialized content
    $song = IOFactory::fromSerialized($content);


See :ref:`all available shortcuts for IOFactory <ref.iofactory>`.


PhpTabs
=======

After PhpTabs has been instanciated, you may call a parser.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $content = file_get_contents('my-file.gp5');

    $song = new PhpTabs();

    // Parse a Guitar Pro string
    $song->fromString($content, 'gp5');

    echo $song->getName();


.. warning ::
    All modifications that you made before a ``fromString()`` call will
    be erased, including meta informations.


The `fromString()` method returns a PhpTabs instance.

.. code-block:: php

    use PhpTabs\IOFactory;

    $content = file_get_contents('my-file.gp5');

    // Render as ASCII in one line
    echo IOFactory::create()                    // PhpTabs
                  ->fromString($content, 'gp5') // PhpTabs
                  ->toAscii();                  // string
