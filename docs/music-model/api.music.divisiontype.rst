.. _api.music.divisiontype:

============
DivisionType
============

DivisionType's parent is :ref:`Duration <api.music.duration>`.

Read division type informations
===============================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a division from a time signature division
    $division = $song->getMeasureHeader(0)
                          ->getTimeSignature()
                          ->getDenominator()
                          ->getDivision();

    echo sprintf("
    DivisionType
    ------------

    enters: %s
    times: %s
    ",

    $division->getEnters(),
    $division->getTimes()
    );

It will ouput something like:

.. code-block:: console

    DivisionType
    ------------

    enters: 1
    times: 1


------------------------------------------------------------------------

Write division type informations
================================

.. code-block:: php

    $division->setEnters(2);
    $division->setTimes(2);


------------------------------------------------------------------------

Compare
=======

You may compare current division type to another one.


.. code-block:: php

    $bool = $division->isEqual($division);

------------------------------------------------------------------------

Copy
====

You may copy all attributes from another division.


.. code-block:: php

    // Copy from another division
    $newDivision>copyFrom($division);
