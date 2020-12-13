.. _api.phptabs:

=======
PhpTabs
=======

PhpTabs provides some methods to access metadata, attributes and nodes.


Read song informations
======================

You may read metadata with the following methods. They all return
string or null.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Display all metas
    echo sprintf("
    Title: %s
    Album: %s
    Artist: %s
    Author: %s
    Writer: %s
    Date: %s
    Copyright: %s
    Transcriber: %s
    Comments: %s",

        $song->getName(),
        $song->getAlbum(),
        $song->getArtist(),
        $song->getAuthor(),
        $song->getWriter(),
        $song->getDate(),
        $song->getCopyright(),
        $song->getTranscriber(),
        $song->getComments(),
    );

It will ouput something like:

.. code-block:: console

    Title: Song title
    Album: My album
    Artist: Me and my band
    Author: Me and my band too
    Writer: A writer
    Date: A long time ago
    Copyright: So cheap
    Transcriber: 
    Comments: Some multiline comments


------------------------------------------------------------------------

Write song informations
=======================

For each getter method, a setter is available.

.. code-block:: php

    $song->setName('New song title');
    $song->setAlbum('Song album');
    $song->setArtist('Song artist');
    $song->setAuthor('Song author');
    $song->setWriter('Song writer');
    $song->setDate('Song date');
    $song->setComments('Song comments');
    $song->setCopyright('Song copyright');


------------------------------------------------------------------------

Channels
========

You may handle :ref:`channels <api.music.channel>`.

.. code-block:: php

    // Number of channels
    $count = $song->countChannels();

    // Get an array of channels
    $channels = $song->getChannels();

    // Get a single channel by its index
    // starting from 0 to n-1
    $channel = $song->getChannel(0);

    // Get a single channel by its id (integer)
    $channel = $song->getChannelById(1);

    // Remove a channel
    $song->removeChannel($channel);

    // Add a channel
    $song->addChannel($channel);

------------------------------------------------------------------------

Measure headers
===============

You may handle :ref:`measure headers <api.music.measureheader>`.

.. code-block:: php

    // Number of measure headers
    $count = $song->countMeasureHeaders();

    // Get an array of measure headers
    $measureHeaders = $song->getMeasureHeaders();

    // Get a single measure header by its index
    // starting from 0 to n-1
    $measureHeader = $song->getMeasureHeader(0);

    // Remove a measure header
    $song->removeMeasureHeader($measureHeader);

    // Add a measure header
    $song->addMeasureHeader($measureHeader);

------------------------------------------------------------------------

Tracks
======

You may handle :ref:`tracks <api.music.track>`.

.. code-block:: php

    // Number of tracks
    $count = $song->countTracks();

    // Get an array of tracks
    $tracks = $song->getTracks();

    // Get a single track by its index
    // starting from 0 to n-1
    $track = $song->getTrack(0);

    // Remove a track
    $song->removeTrack($track);

    // Add a track
    $song->addTrack($track);
