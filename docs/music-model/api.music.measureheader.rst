.. _api.music.measureheader:

=============
MeasureHeader
=============

MeasureHeader's parent is :ref:`PhpTabs <api.phptabs>`.

Read measure header informations
================================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get the first measure header
    $measureHeader = $song->getMeasureHeader(0);

    echo sprintf("
    MeasureHeader
    ----------------

    number: %s
    repeat close: %s
    repeat alternative: %s
    is repeat open: %s
    start: %s
    triplet feel: %s
    length: %s
    ",

        $measureHeader->getNumber(),
        $measureHeader->getRepeatClose(),
        $measureHeader->getRepeatAlternative(),
        $measureHeader->isRepeatOpen() ? 'true' : 'false',
        $measureHeader->getStart(),
        $measureHeader->getTripletFeel(),
        $measureHeader->getLength()
    );

It will ouput something like:

.. code-block:: console

    MeasureHeader
    ----------------

    number: 1
    repeat close: 0
    repeat alternative: 0
    is repeat open: false
    start: 960
    triplet feel: 1
    length: 3840



------------------------------------------------------------------------

Write measure header informations
==================================

For each getter methods, a setter is available.

.. code-block:: php

    $measureHeader->setNumber(1);
    $measureHeader->setRepeatClose(1);
    $measureHeader->setRepeatAlternative(1);
    $measureHeader->setRepeatOpen(1);
    $measureHeader->setStart(480);
    $measureHeader->setTripletFeel(2);


------------------------------------------------------------------------

Marker
======

You may handle :ref:`marker <api.music.marker>`.

.. code-block:: php

    // Get marker
    $marker = $measureHeader->getMarker();

    // Does this measure header has a marker ?
    $bool = $measureHeader->hasMarker();

    $measureHeader->setMarker($marker);

------------------------------------------------------------------------

Tempo
======

You may handle :ref:`tempo <api.music.tempo>`.

.. code-block:: php

    $tempo = $measureHeader->getTempo();

    $measureHeader->setTempo($tempo);

------------------------------------------------------------------------

TimeSignature
=============

You may handle :ref:`time signature <api.music.timesignature>`.

.. code-block:: php

    $timeSignature = $measureHeader->getTimeSignature();

    $measureHeader->setTimeSignature($timeSignature);


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another measure header.


.. code-block:: php

    // Copy from another measure header
    $newHeader->copyFrom($measureHeader);
