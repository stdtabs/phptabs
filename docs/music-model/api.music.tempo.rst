.. _api.music.tempo:

=====
Tempo
=====

Tempo's parent is :ref:`PhpTabs <api.measureheader>`.

Read tempo informations
=======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get the first measure header's tempo
    $tempo = $song->getMeasureHeader(0)->getTempo();

    echo sprintf("
    Tempo
    -----

    value: %s
    in ms: %s
    time per quarter: %s
    ",

        $tempo->getValue(),
        $tempo->getInMillis(),
        $tempo->getInTPQ()
    );

It will ouput something like:

.. code-block:: console

    Tempo
    -----

    value: 200
    in ms: 300
    time per quarter: 300000


------------------------------------------------------------------------

Write tempo informations
========================

.. code-block:: php

    $tempo->setValue(100);


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another tempo.


.. code-block:: php

    // Copy from another tempo
    $newTempo->copyFrom($tempo);
