.. _api.music.text:

====
Text
====

Text's parent is :ref:`Beat <api.music.beat>`.

Read text informations
======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a text
    $text = $song->getTrack(0)
                 ->getMeasure(0)
                 ->getBeat(0)
                 ->getText();

    echo sprintf("
    Text
    ----

    value: %s
    is empty: %s
    ",

    $text->getValue(),
    $text->isEmpty() ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Text
    ----

    value: My text
    is empty: false


------------------------------------------------------------------------

Write text informations
=======================

.. code-block:: php

    $text->setValue('My other text');

------------------------------------------------------------------------

Beat
=====

You may handle its :ref:`beat <api.music.beat>`.

.. code-block:: php

    // Get text beat
    $beat = $text->getBeat();

    // Set text beat
    $text->setBeat($beat);

------------------------------------------------------------------------

Copy
====

You may copy all attributes from another text.


.. code-block:: php

    // Copy from another text
    $newText->copyFrom($text);
