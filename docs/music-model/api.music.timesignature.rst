.. _api.music.timesignature:

=============
TimeSignature
=============

TimeSignature's parent is :ref:`MeasureHeader <api.music.measureheader>`.

Read time signature informations
================================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get the first measure header's time signature
    $timeSignature = $song->getMeasureHeader(0)->getTimeSignature();

    echo sprintf("
    TimeSignature
    -------------

    numerator: %s", $timeSignature->getNumerator()
    );

It will ouput something like:

.. code-block:: console

    TimeSignature
    -------------

    numerator: 4

------------------------------------------------------------------------

Write time signature informations
=================================

.. code-block:: php

    $tempo->setNumerator(6);


------------------------------------------------------------------------

Duration
========

You may handle :ref:`duration <api.music.duration>`.

.. code-block:: php

    // Get denominator
    $duration = $timeSignature->getDenominator();

    $timeSignature->setDenominator($duration);

------------------------------------------------------------------------

Copy
====

You may copy all attributes from another time signature.


.. code-block:: php

    // Copy from another tempo
    $newTimeSignature->copyFrom($timeSignature);
