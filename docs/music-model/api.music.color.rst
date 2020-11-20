.. _api.music.color:

=====
Color
=====

Color's parent is :ref:`Track <api.music.track>`.

Read color informations
=======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get first track's color
    $color = $song->getTrack(0)->getColor();

    echo sprintf("
    Color
    -----

    red: %s
    green: %s
    blue: %s
    ",

    $color->getR(),
    $color->getG(),
    $color->getB()
    );

It will ouput something like:

.. code-block:: console

    Color
    -----

    red: 255
    green: 0
    blue: 0

------------------------------------------------------------------------

Write color informations
================================

.. code-block:: php

    $color->setR(0);
    $color->setG(255);
    $color->setB(128);


------------------------------------------------------------------------

Compare
=======

You may compare current color to another one.


.. code-block:: php

    $bool = $color->isEqual($color);

------------------------------------------------------------------------

Copy
====

You may copy all attributes from another color.


.. code-block:: php

    // Copy from another color
    $newColor>copyFrom($color);
