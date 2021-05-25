.. _ex.calculate-measure-and-beat-durations-in-seconds:

===============================================
Calculate measure and beat durations in seconds
===============================================

PhpTabs provides some useful methods to calculate durations.

Measure duration
================

To calculate duration in seconds, the formula is
``(Number of beats) * 60 / Tempo``.

The following code prints duration for the first measure of the first
track.

.. code-block:: php

    use PhpTabs\PhpTabs;

    // Instanciate a tablature
    $song = new PhpTabs('myTab.gp5');

    // Take a measure
    $measure = $song->getTrack(0)->getMeasure(0);

    // Calculate duration in seconds
    $duration = 60
        * $measure->getTimeSignature()->getNumerator()
        / $measure->getTempo()->getValue();

    // Print duration
    echo sprintf("Duration=%ss", $duration);


With a 4/4 time signature and a tempo of 120, this example will ouput:

.. code-block:: console

    Duration=2s

------------------------------------------------------------------------

Beat duration
=============

Calculating beat duration in seconds is quite more complex. It depends
on:

- tempo
- time signature
- non dotted, dotted or double dotted beat
- division type

PhpTabs (>=0.6.0) provides a shortcut method to easily get this value.

The ``getTime()`` method is provided by the ``PhpTabs\Music\Voice``
model.

The following code prints duration for the first beat of the first
measure of the first track.

.. code-block:: php

    use PhpTabs\PhpTabs;

    // Instanciate a tablature
    $song = new PhpTabs('myTab.gp5');

    // Take a beat
    $beat = $song->getTrack(0)->getMeasure(0)->getBeat(0);

    // Get duration in seconds for the first voice
    $duration = $beat->getVoice(0)->getTime();

    // Print duration
    echo sprintf("Duration=%ss", $duration);


With a 4/4 time signature, a tempo of 120 and for a quarter beat, this
example will ouput:

.. code-block:: console

    Duration=0.5s

------------------------------------------------------------------------

Song duration
=============

In order to calculate the duration of the complete song, we will add up
the durations of each measure.

For one measure, the duration is calculated with the formula:
``(Number of beats) * 60 / Tempo``.

.. code-block:: php

    use PhpTabs\PhpTabs;

    // Instanciate a tablature
    $song = new PhpTabs('myTab.gp5');

    $total = 0;

    // Take all measures for the first track
    $measures = $song->getTrack(0)->getMeasures();

    // Add up durations for each measure
    foreach ($measures as $measure) {

        // Calculate duration in seconds
        $total += 60
            * $measure->getTimeSignature()->getNumerator()
            / $measure->getTempo()->getValue();

    }

    // Print duration
    echo sprintf("Duration=%ss\n", $total);


With a 4/4 time signature, a tempo of 120 and 10 measures, this example
would ouput:

.. code-block:: console

    Duration=20s
