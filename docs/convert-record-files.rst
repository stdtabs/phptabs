=========================
Convert and save to files
=========================

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
 midi, json, ser) and the song is implicitly converted to this format.

If the file extension is not recognized, a format may be passed as the
second parameter.

Of course, you may read, convert and save in one line.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // The Guitar Pro file is parsed, converted and recorded into
    // PHP serialized
    $song = IOFactory::fromFile($filename)
                     ->toFile('my-file.dat, 'ser');


PhpTabs::build($format)
=======================

Sometimes, for debugging or to store content another way, you may want
to output a converted string to a variable.

You may do that with the `build` method.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // The Guitar Pro file is parsed, converted and returned as MIDI
    // content
    $midi = IOFactory::fromFile($filename)->build('mid');



PhpTabs::save($filename)
========================

This way only works for Guitar Pro 3, 4 and 5, MIDI files.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    $song->save('my-file.mid);

