.. _target-track-measure:

===========================
Target a track or a measure
===========================

You may need to target a track or a measure to generate a new complete
song.

PhpTabs provides 2 methods to extract a particular track or a particular
measure and obtain a new PhpTabs instance.


onlyTrack
=========

The method ``onlyTrack`` returns a new ``PhpTabs`` only with the targeted
track. It accepts a track index as parameter.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Get the new song with only the third track
    $new = $song->onlyTrack(2);

    // Saving to Guitar Pro 5 file
    $new->save('3rd-track-of-my-file.gp5'); 

If you only want to work with a particular track without generating a
new song, you may need to have a look to ``PhpTabs->getTrack()`` method.


onlyMeasure
===========

The method ``onlyMeasure`` returns a new ``PhpTabs`` only with the targeted
measure for each track. It accepts a measure index as parameter.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Get the new song with only the third measure for each track
    $new = $song->onlyMeasure(2);

If you only want to work with a particular measure without generating a
new song, you may need to have a look to
``PhpTabs->getTrack(0)->getMeasure(0)`` method.


Chaining onlyTrack and onlyMeasure
==================================

You may want to display only one measure for a particular track. In the
example below, we'll render the first measure of the first track as an
ASCII tab.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // Read and parse file
    $song = IOFactory::fromFile($filename);

    // Display track#0 measure#0 as ASCII
    echo $song->onlyTrack(0)->onlyMeasure(0)->toAscii();


Of course, you may do the same thing in one line (Parse file, 
target a track, target a measure and render).

.. code-block:: php

    echo PhpTabs\IOFactory::fromFile('my-file.gp5')
                   ->onlyTrack(0) 
                   ->onlyMeasure(0)
                   ->toAscii();