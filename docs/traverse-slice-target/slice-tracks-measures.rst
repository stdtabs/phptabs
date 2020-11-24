.. _slice-tracks-measures:

========================
Slice tracks or measures
========================

You may need to slice tracks or measures to generate a new complete
song.

PhpTabs provides 2 methods to extract ranges of tracks or measures to
obtain a new PhpTabs instance.


sliceTracks
===========

The method ``sliceTracks`` returns a new ``PhpTabs`` with the targeted
tracks.

It requires 2 parameters :

- fromTrackIndex
- toTrackIndex

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Get a new song with third and fourth tracks
    $new = $song->sliceTracks(2, 3);

    // Saving to Guitar Pro 5 file
    $new->save('3rd-and-4th-tracks-of-my-file.gp5'); 

If you only want to work with tracks without generating a new song, you
may need to have a look to ``PhpTabs->getTracks()`` method.


sliceMeasures
=============

The method ``sliceMeasures`` returns a new ``PhpTabs`` with the targeted
measures for each track.

It accepts 2 parameters :

- fromMeasureIndex
- toMeasureIndex

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Get a new song with the third, fourth
    // and fifth measures for each track
    $new = $song->sliceMeasures(2, 4);

If you only want to work with measures without generating a new song,
you may need to have a look to
``PhpTabs->getTrack(0)->getMeasures()`` method.


Chaining sliceTracks and sliceMeasures
======================================

You may want to display only some measures from particular tracks. 

In the example below, we'll render the first and second measures 
of the first and second tracks as an ASCII tab.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Display tracks #0 and #1, measures #0 and #1 as ASCII
    echo $song->sliceTracks(0, 1)->sliceMeasures(0, 1)->toAscii();

You may do the same thing in one line (Parse file, slice tracks, 
slice measures and render).

.. code-block:: php

    echo PhpTabs\IOFactory::fromFile('my-file.gp5')
                   ->sliceTracks(0, 1) 
                   ->sliceMeasures(0, 1)
                   ->toAscii();
