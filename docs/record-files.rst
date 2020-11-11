.. _save-files:

=============
Save to files
=============

PhpTabs::toFile($filename)
==========================

The `toFile` method may be used to store file contents on a disk.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    $song->toFile('my-file.mid');


The destination format is recognized with extension (gp3, gp4, gp5, mid,
midi, json, ser, yml, xml) and the song is implicitly converted to this
format.

If the file extension is not recognized, a format may be passed as the
second parameter.

Of course, you may read, convert and save in one line.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // The Guitar Pro file is parsed, converted
    // and recorded into as a PHP serialized string
    $song = IOFactory::fromFile($filename)
                     ->toFile('my-file.dat', 'ser');


See :ref:`all available options <convert-methods>` for explicit conversions.


PhpTabs::save($filename)
========================

This way works too but does not accept a specific parser as second
parameter.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    $song->save('my-file.mid');

