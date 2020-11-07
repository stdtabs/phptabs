=============
Render a song
=============

ASCII tabs
==========

ASCII tablature is a must-have feature, PhpTabs (>= 0.6.0) can render a
whole song or a single track as an ASCII string.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Render the whole song
    echo $song->toAscii();


There are a couple of options that can be passed to `toAscii` method.

See :ref:`renderer.ascii` for more options.


Vextab rendering
================

PhpTabs (>= 0.5.0) can render a track as a VexTab string.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Render the first track
    echo $song->toVextab(0);


Some options can be passed to `toVextab` method.

See :ref:`renderer.vextab` for more options.
