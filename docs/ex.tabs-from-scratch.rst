.. _ex.tabs-from-scratch:

==========================
Create a song from scratch
==========================

Very minimalistic tablature
===========================

The shortest way to create a tablature is to instanciate a PhpTabs.

.. code-block:: php

    use PhpTabs\PhpTabs;

    // Instanciate a tablature
    $song = new PhpTabs();

    // Read some information
    echo $song->getName();


This is only sufficient to make basic operations but it failed if we
want to save it as Guitar Pro or MIDI format.


A minimal and working tablature
===============================

We will create a tablature of one track with one measure.

In addition to the track, we have to define a channel, a measure header
and a measure.

.. code-block:: php

    use PhpTabs\Music\Channel;
    use PhpTabs\Music\Measure;
    use PhpTabs\Music\MeasureHeader;
    use PhpTabs\Music\TabString;
    use PhpTabs\Music\Track;
    use PhpTabs\PhpTabs;

    // Instanciates a tablature
    $tablature = new PhpTabs();

    /* --------------------------------------------------
     | Create basic objects
     | -------------------------------------------------- */

    // Create one track 
    $track = new Track();
    $track->setName('My first track');

    // Define 6 strings
    foreach ([64, 59, 55, 50, 45, 40] as $index => $value) {
        $string = new TabString($index + 1, $value);
        $track->addString($string);
    }

    // One channel
    $channel = new Channel();
    $channel->setId(1);

    // One measure header, will be shared
    // by all first measures of all tracks
    $mh = new MeasureHeader();
    $mh->setNumber(1);

    // One specific measure for the first track,
    // with a MeasureHeader as only parameter
    $measure = new Measure($mh);

    /* --------------------------------------------------
     | Bound them together
     | -------------------------------------------------- */
     
    // Attach channel to the song
    $tablature->addChannel($channel);

    // Attach measure header to the song
    $tablature->addMeasureHeader($mh);

    // Add measure to the track
    $track->addMeasure($measure);

    // Bound track and its channel configuration
    $track->setChannelId($channel->getId());

    // Finally, attach Track to the Tablature container
    $tablature->addTrack($track);


    // Now we can save, convert, export a fully functional song
    $tablature->save('test.gp5');


Note that objects could have been instanciated in a different order.
An approach would have been to create all measure headers first.
Then to create measures for several tracks.
Finally, we could have created the tracks and their channels in order to
integrate everything. 


A working tablature with several tracks and measures
====================================================

We've seen how to create a basic tablature. It's time to build a more
complex tablature.

Let's set our goals:

- One song called 'My song with notes'
- 2 tracks, one for a Piano and one for a Contrabass
- 2 measures per track and one note per measure

.. code-block:: php

    use PhpTabs\Music\Beat;
    use PhpTabs\Music\Channel;
    use PhpTabs\Music\Measure;
    use PhpTabs\Music\MeasureHeader;
    use PhpTabs\Music\Note;
    use PhpTabs\Music\TabString;
    use PhpTabs\Music\Track;
    use PhpTabs\PhpTabs;

    // Instanciate a tablature
    $tablature = new PhpTabs();

    // Set song name
    $tablature->setName('My song with notes');

    /* --------------------------------------------------
     | Create basic objects
     | -------------------------------------------------- */

    // Create tracks
    $piano_track = new Track();
    $piano_track->setName('Piano track');

    $contrabass_track = new Track();
    $contrabass_track->setName('Contrabass track');


    // Create channels
    $channel0 = new Channel();
    $channel0->setId(1);
    $channel0->setProgram(0); // This program is for piano
    $channel1 = new Channel();
    $channel1->setId(2);
    $channel1->setProgram(43); // This program is for contrabass

    // One measure header for each measure
    $mh0 = new MeasureHeader();
    $mh0->setNumber(1);
    $mh1 = new MeasureHeader();
    $mh1->setNumber(2);

    // 2 measures for the first track
    $track0_measure0 = new Measure($mh0);
    $track0_measure1 = new Measure($mh1);

    // 2 measures for the second track
    $track1_measure0 = new Measure($mh0);
    $track1_measure1 = new Measure($mh1);

    /* --------------------------------------------------
     | Add notes for each measure
     | -------------------------------------------------- */
    foreach ([
        $track0_measure0,
        $track0_measure1,
        $track1_measure0,
        $track1_measure1
    ] as $measure) {
        // Create a Beat and a Note
        $beat = new Beat();
        $note = new Note();
        // Attach note to the beat
        $beat->getVoice(0)->addNote($note);
        // Make a random value for the note
        $note->setValue(rand(0, 5));
        // Attach beat to the measure
        $measure->addBeat($beat);
    }
        

    /* --------------------------------------------------
     | Bound headers, channels, measures and tracks
     | -------------------------------------------------- */
     
    // Attach channels to the song
    $tablature->addChannel($channel0);
    $tablature->addChannel($channel1);

    // Attach measure headers to the song
    $tablature->addMeasureHeader($mh0);
    $tablature->addMeasureHeader($mh1);

    // Add measures to the first track
    $piano_track->addMeasure($track0_measure0);
    $piano_track->addMeasure($track0_measure1);
    $piano_track->addString(new TabString(1, 64));

    // Add measures to the second track
    $contrabass_track->addMeasure($track1_measure0);
    $contrabass_track->addMeasure($track1_measure1);
    $contrabass_track->addString(new TabString(1, 64));

    // Bound tracks and their channel configurations
    $piano_track->setChannelId($channel0->getId());
    $contrabass_track->setChannelId($channel1->getId());

    // Finally, attach Tracks to the Tablature container
    $tablature->addTrack($piano_track);
    $tablature->addTrack($contrabass_track);

    /* --------------------------------------------------
     | Now that we have a functionnal song, we can work
     | with it
     | -------------------------------------------------- */

    // Render the first track as a vextab string
    echo $tablature->getRenderer('vextab')->render(0);

    // Render the second track as an ASCII string
    echo $tablature->getRenderer('ascii')->render(1);

    // Save it as a Guitar Pro 5 file
    $tablature->save('song-2-tracks-2-measures.gp5');


Some important things to keep in mind:

- Measure headers are defined globally (attached to the Song)
- Measures are defined per track
- you MUST have the same number of measures for each track
- This number of measures MUST be equal to the number of measure headers
- For all tracks, a measure number 1 MUST be bound to the measure header
  number 1, and so on for all measures
- To understand how elements are built on each other and how to be on
  the right scope to interact with them, refer to the
  :ref:`Music stack tree <ref.music-model>` and to the
  :ref:`getting/setting/counting rules <traverse.songs>`.
