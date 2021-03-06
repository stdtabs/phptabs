.. _traverse.songs:

=====================
Traverse a whole song
=====================

PhpTabs makes a song fully-traversable.

Starting from one point, you may find your way with the
:ref:`Music tree <ref.music-model>`.

Traversing data is made with getter and counter methods.

A traversal is done in read-write mode

Getter and counter rules
========================

There are 4 rules for getter names:

1. ``get + {objectName} + ()``

   It's a ``property getter`` method.
   
   ie: a measure header may only contain one Tempo, 
   so the method name to get the tempo for a given measure is
   ``$header->getTempo()``.

2. ``count + {objectName} + s()``

   It's a ``nodes counter`` method.
   
   ie: a track may contain several measures,
   so the method name to count them is ``$track->countMeasures()``
  
3. ``get + {objectName} + s()``

   It's a collection getter method, it returns an array with all
   nodes.
   
   ie: a track may contain several measures, so the method name to
   get them is ``$track->getMeasures()``.

4. ``get + {objectName} + ($index)``

   It gets a node from a collection by its index.
   ``$index`` is starting from 0 to n-1, with n=child count (returned by
   the counter method)
   
   ie: there can be several measures per Track, so the method name to
   get one measure (the first) is ``$track->getMeasure(0)``

When in doubt, reference should be made to the
:ref:`Music-Model reference <ref.music-model>`

------------------------------------------------------------------------

Traversing example
==================

In the following example, we'll traverse all tracks, all measures and
all beats, the goal is to print all notes.

.. code-block:: php

    use PhpTabs\Music\Note;
    use PhpTabs\PhpTabs;

    $tab = new PhpTabs('mytab.gp4');

    # Get all tracks
    foreach ($tab->getTracks() as $track) {
        # Get all measures
        foreach ($track->getMeasures() as $measure) {
            # Get all beats
            foreach ($measure->getBeats() as $beat) {
                # Get all voices
                foreach ($beat->getVoices() as $voice) {
                    # Get all notes
                    foreach ($voice->getNotes() as $note) {

                        printNote($note);

                    }
                }
            }
        }
    }

    /**
     * Print all referential
     * based on the note model
     *
     * @param \PhpTabs\Music\Note $note
     */
    function printNote(Note $note)
    {
        echo sprintf(
            "\nTrack %d - Measure %d - Beat %d - Voice %d - Note %s/%s",
            $note->getVoice()->getBeat()->getMeasure()->getTrack()->getNumber(),
            $note->getVoice()->getBeat()->getMeasure()->getNumber(),
            $note->getVoice()->getBeat()->getStart(),
            $note->getVoice()->getIndex(),
            $note->getValue(),
            $note->getString()
      );
    }


will output something like

.. code-block:: console

    Track 1 - Measure 1 - Beat 6240 - Voice 0 - Note 11/3
    Track 1 - Measure 1 - Beat 6480 - Voice 0 - Note 0/2

    [...]

    Track 2 - Measure 1 - Beat 960 - Voice 0 - Note 5/2
    Track 2 - Measure 1 - Beat 1920 - Voice 0 - Note 5/2
    Track 2 - Measure 1 - Beat 2880 - Voice 0 - Note 5/2
    Track 2 - Measure 1 - Beat 3840 - Voice 0 - Note 5/2

    [...]


All referential can be accessed starting from a note.

Let's rewrite the printNote function in a more readable way.

.. code-block:: php

    /**
     * Print all referential
     *
     * @param \PhpTabs\Music\Track   $track
     * @param \PhpTabs\Music\Measure $measure
     * @param \PhpTabs\Music\Beat    $beat
     * @param \PhpTabs\Music\Voice   $voice
     * @param \PhpTabs\Music\Note    $note
     */
    function printNote($track, $measure, $beat, $voice, $note)
    {
        echo sprintf(
            "\nTrack %d - Measure %d - Beat %d - Voice %d - Note %s/%s",
            $track->getNumber(),
            $measure->getNumber(),
            $beat->getStart(),
            $voice->getIndex(),
            $note->getValue(),
            $note->getString()
      );
    }


This example does not take into account some aspects of the referential
such as rest beats, durations, dead notes, note effects and chord beats.
