.. _api.music.beat:

====
Beat
====

Beat's parent is :ref:`Measure <api.music.measure>`.

Read beat informations
======================

.. code-block:: php

   use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a beat
    $beat = $song->getTrack(0)
                    ->getMeasure(0)
                    ->getBeat(0);

    echo sprintf("
    Beat
    ----

    start: %s
    is chord beat: %s
    is rest beat: %s
    is text beat: %s
    ",

    $beat->getStart(),
    $beat->isChordBeat() ? 'true' : 'false',
    $beat->isRestBeat() ? 'true' : 'false',
    $beat->isTextBeat() ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Beat
    ----

    start: 960
    is chord beat: false
    is rest beat: true
    is text beat: false

------------------------------------------------------------------------

Write beat informations
=======================

.. code-block:: php

    $beat->setStart(1920);

------------------------------------------------------------------------

Measure
=======

You may handle its :ref:`measure <api.music.measure>`.

.. code-block:: php

    $measure = $beat->getMeasure();

    $beat->setMeasure($measure);

------------------------------------------------------------------------

Chord
=======

You may handle its :ref:`chord <api.music.chord>`.

.. code-block:: php

    $chord = $beat->getChord();

    $beat->setChord($chord);

------------------------------------------------------------------------

Text
=======

You may handle its :ref:`text <api.music.text>`.

.. code-block:: php

    $text = $beat->getText();

    $beat->setText($text);

------------------------------------------------------------------------

Stroke
=======

You may handle its :ref:`stroke <api.music.stroke>`.

.. code-block:: php

    $stroke = $beat->getStroke();

------------------------------------------------------------------------

Voices
======

You may handle :ref:`voices <api.music.voice>`.

.. code-block:: php

    // Number of voices
    $count = $beat->countVoices();

    // Get an array of voices
    $voices = $beat->getVoices();

    // Get a single voice by its index
    // starting from 0 to n-1
    $voice = $beat->getVoice(0);

    // Set a voice by index (0 or 1)
    $beat->setVoice(1, $voice);
