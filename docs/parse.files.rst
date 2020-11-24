.. _parse.files:

================
Parse from files
================

There are 2 ways to parse a file.

IOFactory
=========

An IOFactory class is provided.

After a read operation, a ``PhpTabs`` containing the entire song is
returned.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    $song = IOFactory::fromFile($filename);

    echo $song->getName();

The file format is recognized by the file extension.

The following formats are available:

- ``gp3`` for Guitar Pro 3
- ``gp4`` for Guitar Pro 4
- ``gp5`` for Guitar Pro 5
- ``mid`` or ``midi`` for MIDI
- ``json``
- ``ser`` for PHP serialized string

If the file extension is not standard, a parser can be specified 
as the second parameter to force a file format.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.dat';

    // The file is PHP serialized
    $song = IOFactory::fromFile($filename, 'ser');

    echo $song->getName();

``IOFactory`` offers :ref:`some other shortcuts <ref.iofactory>`.


PhpTabs
=======

This way works as well as IOFactory but you may not specify a
parser.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    echo $song->getName();

