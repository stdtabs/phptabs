.. _api.music.voice:

=====
Voice
=====

Voice's parent is :ref:`Voice <api.music.voice>`.

Read voice informations
=======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a voice
    $voice = $song->getTrack(0)
                 ->getMeasure(1)
                 ->getBeat(0)
                 ->getVoice(0);

    echo sprintf("
    Voice
    -----

    direction: %s
    index: %s
    time: %ss
    is empty: %s
    is rest voice: %s
    ",

    $voice->getDirection(),
    $voice->getIndex(),
    $voice->getTime(),
    $voice->isEmpty() ? 'true' : 'false',
    $voice->isRestVoice() ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Voice
    -----

    direction: 0
    index: 0
    time: 2.0454545454545s
    is empty: false
    is rest voice: false


------------------------------------------------------------------------

Write voice informations
========================

.. code-block:: php

    /**
     * Defaut = 0
     * NONE   = 0
     * UP     = 1
     * DOWN   = 2
     */
    $voice->setDirection(1);
    $voice->setIndex(1);

------------------------------------------------------------------------

Beat
====

You may handle its :ref:`beat <api.music.beat>`.

.. code-block:: php

    // Get voice beat
    $beat = $voice->getBeat();

    // Set voice beat
    $voice->setBeat($beat);

------------------------------------------------------------------------

Notes
========

You may handle :ref:`notes <api.music.note>`.

.. code-block:: php

    // Number of notes
    $count = $voice->countNotes();

    // Get an array of notes
    $notes = $voice->getNotes();

    // Get a single note by its index
    // starting from 0 to n-1
    $note = $voice->getNote(0);

    // Remove a note
    $voice->removeNote($note);

    // Add a note
    $voice->addNote($note);
