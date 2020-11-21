.. _api.music.chord:

=====
Chord
=====

Chord's parent is :ref:`Beat <api.music.beat>`.

Read chord informations
=======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a chord
    $chord = $song->getTrack(0)
                  ->getMeasure(0)
                  ->getBeat(0)
                  ->getChord();

    echo sprintf("
    Chord
    -----

    name: %s
    first fret: %s
    notes count: %s
    strings count: %s
    ",

    $chord->getName(),
    $chord->getFirstFret(),
    $chord->countNotes(),
    $chord->countStrings()
    );


It will ouput something like:

.. code-block:: console

    Chord
    -----

    name: A
    first fret: 0
    notes count: 0
    strings count: 6


------------------------------------------------------------------------

Write chord informations
========================

.. code-block:: php

    $chord->setName('C');
    $chord->setFirstFret(1);

------------------------------------------------------------------------

Beat
====

You may handle its :ref:`beat <api.music.beat>`.

.. code-block:: php

    $beat = $chord->getBeat();

    $chord->setBeat($beat);
