.. _api.music.measure:

=======
Measure
=======

Measure's parent is :ref:`Track <api.music.track>`.

Read measure informations
=========================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get first measure
    $measure = $song->getTrack(0)
                   ->getMeasure(0);

    echo sprintf("
    Measure
    -------

    clef: %s
    key signature: %s
    length: %s
    number: %s
    repeat close: %s
    start: %s
    triplet feel: %s
    is repeat open: %s
    ",

    $measure->getClef(),
    $measure->getKeySignature(),
    // Get measure header length
    $measure->getLength(),
    // Get measure header number
    $measure->getNumber(),
    // Get measure header repeat close
    $measure->getRepeatClose(),
    // Get measure header start
    $measure->getStart(),
    // Get measure header triplet feel
    $measure->getTripletFeel(),
    // Get measure header is repeat open
    $measure->isRepeatOpen() ? 'true' : 'false',
    
    );

It will ouput something like:

.. code-block:: console

    Measure
    -------

    clef: 1
    key signature: 0
    length: 3840
    number: 1
    repeat close: 0
    start: 960
    triplet feel: 1
    is repeat open: false

------------------------------------------------------------------------

Write measure informations
==========================

.. code-block:: php

    /**
     * Default clef = 1
     * TREBLE = 1
     * BASS   = 2
     * TENOR  = 3
     * ALTO   = 4
     */
    $measure->setClef(2);

    $measure->setKeySignature(1);


------------------------------------------------------------------------

Track
=====

You may get and set its parent :ref:`track <api.music.track>`.


.. code-block:: php

    $track = $measure->getTrack();

    $measure->setTrack($track);


------------------------------------------------------------------------

Beats
=====

You may handle :ref:`beats <api.music.beat>`.

.. code-block:: php

    // Number of beats
    $count = $measure->countBeats();

    // Get an array of beats
    $beats = $measure->getBeats();

    // Get a single beat by its index
    // starting from 0 to n-1
    $beat = $measure->getBeat(0);

    $beat = $measure->getBeatByStart(960);

    // Move a beat to another index in the stack
    $measure->moveBeat(4, $beat);

    // Remove a beat
    $measure->removeBeat($beat);

    // Add a beat
    $measure->addBeat($beat);

------------------------------------------------------------------------

MeasureHeader
=============

You may handle :ref:`measure header <api.music.measureheader>`.

.. code-block:: php

    $header = $measure->getHeader();

    $measure->setHeader($header);


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another measure.


.. code-block:: php

    // Copy from another measure
    $newMeasure>copyFrom($measure);
