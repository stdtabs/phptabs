.. _api.music.duration:

========
Duration
========

Duration's parent is :ref:`TimeSignature <api.music.timesignature>`.

Read duration informations
==========================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a time signature duration
    $duration = $song->getMeasureHeader(0)
                          ->getTimeSignature()
                          ->getDenominator();

    echo sprintf("
    Duration
    --------

    index: %s
    value: %s
    time: %s
    is dotted: %s
    is double-dotted: %s
    
    ",

    $duration->getIndex(),
    $duration->getValue(),
    $duration->getTime(),
    $duration->isDotted() ? 'true' : 'false',
    $duration->isDoubleDotted() ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Duration
    --------

    index: 2
    value: 4
    time: 960
    is dotted: false
    is double-dotted: false


------------------------------------------------------------------------

Write duration informations
===========================

.. code-block:: php

    $duration->setValue(2);
    $duration->setDotted(true);
    $duration->setDoubleDotted(false);


------------------------------------------------------------------------

DivisionType
============

You may handle :ref:`duration <api.music.divisiontype>`.

.. code-block:: php

    // Get division type
    $division = $duration->getDivision();


------------------------------------------------------------------------

Compare
=======

You may compare current duration to another one.


.. code-block:: php

    $bool = $newDuration->isEqual($duration);

------------------------------------------------------------------------

Copy
====

You may copy all attributes from another duration.


.. code-block:: php

    // Copy from another duration
    $newDuration->copyFrom($duration);
