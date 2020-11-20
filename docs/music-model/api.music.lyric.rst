.. _api.music.lyric:

=====
Lyric
=====

Lyric's parent is :ref:`Track <api.music.track>`.

Read lyric informations
=======================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get first track's lyric
    $lyric = $song->getTrack(0)->getLyrics();

    echo sprintf("
    Lyric
    -----

    from: %s
    lyrics: %s
    isEmpty: %s
    ",

    $lyric->getFrom(),
    implode(' | ', $lyric->getLyrics()),
    $lyric->isEmpty() ? 'true' : 'false'
    );

It will ouput something like:

.. code-block:: console

    Lyric
    -----

    from: 1
    lyrics: 
    isEmpty: true


------------------------------------------------------------------------

Write lyric informations
========================

.. code-block:: php

    $lyric->setFrom(480);
    $lyric->setLyrics('Some lyrics');


------------------------------------------------------------------------

Copy
====

You may copy all attributes from another lyric.


.. code-block:: php

    // Copy from another lyric
    $newLyric>copyFrom($lyric);
