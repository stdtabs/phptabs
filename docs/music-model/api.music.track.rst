.. _api.music.track:

=====
Track
=====

PhpTabs provides some methods to access metadata, attributes and nodes.


Read track informations
=======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get the first track
    $track = $song->getTrack(0);

    // Informations
    echo sprintf("
    Track name: %s
    Track number: %s
    Channel id: %s
    Offset: %s
    Is solo: %s
    Is muted: %s
    ",

        $track->getName(),
        $track->getNumber(),
        $track->getChannelId(),
        $track->getOffset(),// int between -24 and 24
        $track->isSolo(),   // bool
        $track->isMute()   // bool
        
    );

It will ouput something like:

.. code-block:: console

    Track name: Me (Clean guitar)
    Track number: 1
    Channel id: 1
    Offset: 0
    Is solo: false
    Is muted: false

------------------------------------------------------------------------

Write track informations
========================

For each getter methods, a setter is available.

.. code-block:: php

    $track->setName("My track name");
    $track->setNumber(1);
    $track->setChannelId(1);
    $track->setOffset(10);  // int between -24 and 24
    $track->setSolo(true);   // bool
    $track->setMute(true);  // bool


------------------------------------------------------------------------

Color
=====

You may handle :ref:`colors <api.music.color>`.

.. code-block:: php

    // Get track color
    // PhpTabs\Music\Color
    $color = $track->getColor();

    // Set track color
    $track->setColor($color);


------------------------------------------------------------------------

Lyrics
======

You may handle :ref:`lyrics <api.music.lyric>`.

.. code-block:: php

    // Get track lyric
    // PhpTabs\Music\Lyric
    $lyrics = $track->getLyrics();

    // Set track lyric
    $track->setLyrics($lyrics);

------------------------------------------------------------------------

Measures
========

You may handle :ref:`measures <api.music.measure>`.

.. code-block:: php

    // Number of measures
    $count = $track->countMeasures();

    // Get an array of measures
    $measures = $track->getMeasures();

    // Get a single measure by its index
    // starting from 0 to n-1
    $measure = $track->getMeasure(0);

    // Remove a measure header
    $track->removeMeasure(0);

    // Add a measure header
    $track->addMeasure($measure);

------------------------------------------------------------------------

Strings
=======

You may handle :ref:`strings <api.music.string>`.

.. warning::

    As ``string`` is a reserved key in PHP, the class name for guitar
    strings is ``TabString``.


.. code-block:: php

    // Number of strings
    $count = $track->countStrings();

    // Get an array of strings
    $strings = $track->getStrings();

    // Get a single string by its index
    // starting from 0 to n-1
    $string = $track->getString(0);

    // Add a string
    $track->addString($string);

    // Add a list of strings
    $track->setStrings([
        $string,
        $string,
        $string,
        $string,
        $string,
        $string
    ]);


------------------------------------------------------------------------

Clear and copy
==============

You may copy all attributes from another track or simply
clear all track informations and nodes.


.. code-block:: php

    // Copy from another track
    $track->copyFrom($track);

    // Clear all the track
    $track->clear();

