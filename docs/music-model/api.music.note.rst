.. _api.music.note:

====
Note
====

Note's parent is :ref:`Voice <api.music.voice>`.

Read note informations
======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a note
    $note = $song->getTrack(0)
                 ->getMeasure(0)
                 ->getBeat(2)
                 ->getVoice(0)
                 ->getNote(0);

    echo sprintf("
    Note
    ----

    string: %s
    value: %s
    velocity: %s
    is tied note: %s
    ",

    $note->getString(),
    $note->getValue(),
    $note->getVelocity(),
    $note->isTiedNote() ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Note
    ----

    string: 3
    value: 11
    velocity: 95
    is tied note: false

------------------------------------------------------------------------

Write note informations
=======================

.. code-block:: php

    $note->setString(1);
    $note->setValue(12);
    $note->setVelocity(24);
    $note->isTiedNote(true);

------------------------------------------------------------------------

Voice
=====

You may handle its :ref:`voice <api.music.voice>`.

.. code-block:: php

    // Get note voice
    $voice = $note->getVoice();

    // Set note voice
    $note->setVoice($voice);

------------------------------------------------------------------------

Effect
======

You may handle its :ref:`effect <api.music.effect>`.

.. code-block:: php

    // Get note effect
    $effect = $note->getEffect();

    // Set note effect
    $note->setEffect($effect);
