====================
Read and parse files
====================

There are 2 ways to parse a file.

IOFactory
=========

This is the preferred method as it can parse more file formats (Guitar
Pro 3, 4 and 5, MIDI, JSON, PHP serialized).

After a read operation, a `PhpTabs` containing the entire song is
returned.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    $song = IOFactory::fromFile($filename);

    echo $song->getName();

The file format is recognized with extension (gp3, gp4, gp5, mid, midi,
json, ser).

If the file extension is not generic, a parser can be passed as the
second parameter to force a file format.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.dat';

    // The file is PHP serialized
    $song = IOFactory::fromFile($filename, 'ser');

    echo $song->getName();

`IOFactory` offers some other shortcuts to force a parser.

.. code-block:: php

    use PhpTabs\IOFactory;

    // Try to read a JSON file
    $tab = IOFactory::fromJsonFile('mytabs.json');

    // Try to read a serialized file
    $tab = IOFactory::fromSerializedFile('mytabs.dat');


PhpTabs
=======

This way only works for Guitar Pro 3, 4 and 5, MIDI files.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    echo $song->getName();

