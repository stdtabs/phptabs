.. _api.music.stroke:

=======
Stroke
=======

Stroke's parent is :ref:`Beat <api.music.beat>`.

Read stroke informations
========================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get stroke for the first beat
    $stroke = $song->getTrack(0)
                    ->getMeasure(0)
                    ->getBeat(0)
                    ->getStroke();

    echo sprintf("
    Stroke
    ------

    direction: %s
    value: %s
    ",

    $stroke->getDirection(),
    $stroke->getValue()
    
    );

It will ouput something like:

.. code-block:: console

    Stroke
    ------

    direction: 0
    value: 
    

------------------------------------------------------------------------

Write stroke informations
=========================

.. code-block:: php

    /**
     * Default direction = 0
     * NONE = 0
     * UP   = 1
     * DOWN = -1
     */
    $stroke->setDirection(1);

    $stroke->setValue(1);


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another stroke.


.. code-block:: php

    // Copy from another stroke
    $newStroke>copyFrom($stroke);
