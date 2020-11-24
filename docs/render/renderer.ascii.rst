.. _renderer.ascii:

======================
Render a song as ASCII
======================

ASCII tablature is a must-have feature, PhpTabs (>= 0.6.0) can
:ref:`render <render.songs>` a whole song, some measures or tracks as
ASCII strings.

Quick usage
===========

The following code prints the whole song's tabstaves. All tracks
are printed.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Parse and render
    // and render as ASCII tabs
    echo IOFactory::fromFile($filename)->toAscii();

This example will ouput something like:

.. code-block:: console

    E|------------------------|-10-----10-----------------------------------------------------|
    B|--------------------X---|-------------13------------------------------------------------|
    G|-%-------%------11------|------------------12---12--10------------10----------%---------|
    D|-%-------%--------------|-------------------------------12--12--------12--10--%---12----|
    A|------------------------|---------------------------------------------------------------|
    E|------------------------|---------------------------------------------------------------|


    E|-------------------------------------|-0-----------3-----------------------|
    B|-5-----5-----5-----5-----5-----5-----|-5-----5-----5-----5-----5-----5-----|
    G|-------------------------------------|-------------5-----------------------|
    D|-------------------------------------|-------------5-----------------------|
    A|-------------------------------------|-------------3-----------------------|
    E|-------------------------------------|-------------3-----------------------|


Available options
=================

The ``toAscii()`` method may take an array of parameters.

=============  =========== =============================================
Name           Default     Description
=============  =========== =============================================
songHeader     false       Display song  metadata
trackHeader    false       Display track number and name
maxLineLength  80          Max length for staves in characters
=============  =========== =============================================

Track informations can be printed with ``trackHeader`` option.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // trackHeader
    echo IOFactory::fromFile($filename)->toAscii([
        'trackHeader' => true,
    ]);

This example will ouput something like:

.. code-block:: console

    Track 1: Guitar
    E|------------------------|-10-----10-----------------------------------------------------|
    B|--------------------X---|-------------13------------------------------------------------|
    G|-%-------%------11------|------------------12---12--10------------10----------%---------|
    D|-%-------%--------------|-------------------------------12--12--------12--10--%---12----|
    A|------------------------|---------------------------------------------------------------|
    E|------------------------|---------------------------------------------------------------|


    Track 2: Voice
    E|-------------------------------------|-0-----------3-----------------------|
    B|-5-----5-----5-----5-----5-----5-----|-5-----5-----5-----5-----5-----5-----|
    G|-------------------------------------|-------------5-----------------------|
    D|-------------------------------------|-------------5-----------------------|
    A|-------------------------------------|-------------3-----------------------|
    E|-------------------------------------|-------------3-----------------------|


Song informations can be printed with ``songHeader`` option.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // trackHeader
    echo IOFactory::fromFile($filename)->toAscii([
        'songHeader' => true,
        'trackHeader' => true,
    ]);

This example will ouput something like:

.. code-block:: console

    Title: Testing name
    Album: Testing album
    Artist: Testing artist
    Author: Testing author

    Track 1: Guitar
    E|------------------------|-10-----10-----------------------------------------------------|
    B|--------------------X---|-------------13------------------------------------------------|
    G|-%-------%------11------|------------------12---12--10------------10----------%---------|
    D|-%-------%--------------|-------------------------------12--12--------12--10--%---12----|
    A|------------------------|---------------------------------------------------------------|
    E|------------------------|---------------------------------------------------------------|


    Track 2: Voice
    E|-------------------------------------|-0-----------3-----------------------|
    B|-5-----5-----5-----5-----5-----5-----|-5-----5-----5-----5-----5-----5-----|
    G|-------------------------------------|-------------5-----------------------|
    D|-------------------------------------|-------------5-----------------------|
    A|-------------------------------------|-------------3-----------------------|
    E|-------------------------------------|-------------3-----------------------|



To format line length as you want, a ``maxLineLength`` option is
available. It represents how many characters can be printed before going
to a new line.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-file.gp5');

    // trackHeader
    echo $song->toAscii([
        'maxLineLength' => 10,
    ]);

This example will ouput something like:

.. code-block:: console

    E|------------------------|
    B|--------------------X---|
    G|-%-------%------11------|
    D|-%-------%--------------|
    A|------------------------|
    E|------------------------|


    E|-10-----10-----------------------------------------------------|
    B|-------------13------------------------------------------------|
    G|------------------12---12--10------------10----------%---------|
    D|-------------------------------12--12--------12--10--%---12----|
    A|---------------------------------------------------------------|
    E|---------------------------------------------------------------|


Slice and render
================

By default, the whole song is rendered. Using
:ref:`slice <slice-tracks-measures>` and
:ref:`only <target-track-measure>`
methods may be useful to target only what you want to display.

Let's see how to render only the first track.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Parse, slice first track
    // and render as ASCII tabs
    echo IOFactory::fromFile($filename) // Parse
                     ->onlyTrack(0)     // Slice
                     ->toAscii();       // Render


Even better, sometimes a track can be so long that you may want to
render only some measures.

In the example below, only the first and second measures of the first
track are rendered.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Parse, target the first track,
    // slice 2 measures
    // and render as ASCII tabs
    echo IOFactory::fromFile($filename)    // Parse
                     ->onlyTrack(0)        // Slice
                     ->sliceMeasures(0, 1) // Slice
                     ->toAscii();          // Render

If you need more explanation, let's have a look at their manual.

:ref:`Slicing tracks and measures <slice-tracks-measures>`

:ref:`Target a single track or a single measure <target-track-measure>`
