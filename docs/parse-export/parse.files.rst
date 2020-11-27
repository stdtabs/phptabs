.. _parse.files:

================
Parse from files
================

There are 2 ways to parse a file.

PhpTabs
=======

The easiest way is to instanciate a PhpTabs with a filename as 
first argument.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    echo $song->getName();

It supports Guitar Pro 3, 4 and 5, MIDI, JSON and PHP 
serialized files.

The file format is recognized by the file extension (gp3, gp4, gp5, mid,
midi, json, ser).

See :ref:`all available PhpTabs methods <api.phptabs>`.

________________________________________________________________________

IOFactory
=========

If you need more control, IOFactory is preferred.

After a read operation, a ``PhpTabs`` containing the entire song is
returned.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    $song = IOFactory::fromFile($filename);

    echo $song->getName();

If the file extension is not standard, a parser can be specified as the
second parameter to force a file format.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.dat';

    // The file is PHP serialized
    $song = IOFactory::fromFile($filename, 'ser');

    echo $song->getName();

``IOFactory`` offers some other shortcuts to load from a specified parser.

.. code-block:: php

    use PhpTabs\IOFactory;

    // Try to read a JSON file
    $tab = IOFactory::fromJsonFile('mytabs.json');

    // Try to read a serialized file
    $tab = IOFactory::fromSerializedFile('mytabs.dat');


See :ref:`all available shortcuts for IOFactory <ref.iofactory>`.
