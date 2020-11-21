.. _api.music.marker:

=====
Marker
=====

Marker's parent is :ref:`Measure <api.music.measure>`.

Read marker informations
========================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get first measure's marker
    $marker = $song->getTrack(0)
                   ->getMeasure(0)
                   ->getMarker();

    echo sprintf("
    Marker
    ------

    title: %s
    ",

    $marker->getTitle()
    );

It will ouput something like:

.. code-block:: console

    Marker
    ------

    title: My marker title



------------------------------------------------------------------------

Write marker informations
=========================

.. code-block:: php

    $marker->setTitle('Some string');


------------------------------------------------------------------------

Color
=====

You may get and set a :ref:`color <api.music.color>`.


.. code-block:: php

    $color = $marker->getColor();

    $marker->setColor($color);


------------------------------------------------------------------------

Measure
=======

You may get and set its parent :ref:`measure <api.music.measure>`.


.. code-block:: php

    $measure = $marker->getMeasure();

    $marker->setMeasure($measure);



------------------------------------------------------------------------

Copy
====

You may copy all attributes from another marker.


.. code-block:: php

    // Copy from another marker
    $newMarker>copyFrom($marker);
