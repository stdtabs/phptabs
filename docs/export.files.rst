.. _export.files:

===============
Export to files
===============

PhpTabs::save($filename, $format = null)
========================================

The ``save()`` method may be used to store file contents on a disk.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    $song->save('my-file.mid');


The destination format is recognized by the file extension (gp3, gp4,
gp5, mid, midi, json, ser, yml, xml) and the song is implicitly
converted to this format.

If the file extension is not recognized, a format may be passed as the
second parameter.

Of course, you may read, convert and save in one line.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // The Guitar Pro file is parsed, converted
    // and recorded as a PHP serialized string
    IOFactory::fromFile($filename)
             ->save('my-file.dat', 'ser');


See :ref:`available formats <export.strings>`.
