.. _api.music.noteeffect:

==========
NoteEffect
==========

NoteEffect's parent is :ref:`Note <api.music.note>`.

Read note effect informations
=============================

.. code-block:: php

    use PhpTabs\PhpTabs;

    $song = new PhpTabs('my-song.gp5');

    // Get a note effect
    $noteEffect = $song->getTrack(0)
                 ->getMeasure(0)
                 ->getBeat(2)
                 ->getVoice(0)
                 ->getNote(0)
                 ->getEffect();

    echo sprintf("
    NoteEffect
    ----------

    has any effect: %s
    is bend: %s
    is tremolobar: %s
    is harmonic: %s
    is grace: %s
    is trill: %s
    is tremolo pîcking: %s
    is vibrato: %s
    is dead note: %s
    is slide: %s
    is hammer: %s
    is ghost note: %s
    is accentuated note: %s
    is heavy accentuated note: %s
    is palm mute: %s
    is let ring: %s
    is staccato: %s
    is tapping: %s
    is slapping: %s
    is popping: %s
    is fade in: %s
    ",

    $noteEffect->hasAnyEffect() ? 'true' : 'false',
    $noteEffect->isBend() ? 'true' : 'false',
    $noteEffect->isTremoloBar() ? 'true' : 'false',
    $noteEffect->isHarmonic() ? 'true' : 'false',
    $noteEffect->isGrace() ? 'true' : 'false',
    $noteEffect->isTrill() ? 'true' : 'false',
    $noteEffect->isTremoloPicking() ? 'true' : 'false',
    $noteEffect->isVibrato() ? 'true' : 'false',
    $noteEffect->isDeadNote() ? 'true' : 'false',
    $noteEffect->isSlide() ? 'true' : 'false',
    $noteEffect->isHammer() ? 'true' : 'false',
    $noteEffect->isGhostNote() ? 'true' : 'false',
    $noteEffect->isAccentuatedNote() ? 'true' : 'false',
    $noteEffect->isHeavyAccentuatedNote() ? 'true' : 'false',
    $noteEffect->isPalmMute() ? 'true' : 'false',
    $noteEffect->isLetRing() ? 'true' : 'false',
    $noteEffect->isStaccato() ? 'true' : 'false',
    $noteEffect->isTapping() ? 'true' : 'false',
    $noteEffect->isSlapping() ? 'true' : 'false',
    $noteEffect->isPopping() ? 'true' : 'false',
    $noteEffect->isFadeIn() ? 'true' : 'false'
    );


It will ouput something like:

.. code-block:: console

    NoteEffect
    ----------

    has any effect: true
    is bend: false
    is tremolobar: false
    is harmonic: false
    is grace: false
    is trill: false
    is tremolo pîcking: false
    is vibrato: false
    is dead note: false
    is slide: false
    is hammer: false
    is ghost note: true
    is accentuated note: false
    is heavy accentuated note: false
    is palm mute: false
    is let ring: false
    is staccato: false
    is tapping: true
    is slapping: false
    is popping: false
    is fade in: false


------------------------------------------------------------------------

Write note effect informations
==============================

.. code-block:: php

    $noteEffect->setVibrato(true);
    $noteEffect->setDeadNote(false);
    $noteEffect->setSlide(false);
    $noteEffect->setHammer(false);
    $noteEffect->setGhostNote(false);
    $noteEffect->setAccentuatedNote(false);
    $noteEffect->setHeavyAccentuatedNote(false);
    $noteEffect->setPalmMute(false);
    $noteEffect->setLetRing(false);
    $noteEffect->setStaccato(false);
    $noteEffect->setTapping(false);
    $noteEffect->setSlapping(false);
    $noteEffect->setPopping(false);
    $noteEffect->setFadeIn(false);

------------------------------------------------------------------------

EffectBend
==========

You may handle its :ref:`EffectBend <api.music.effectbend>`.

.. code-block:: php

    // Get effect bend
    $effectBend = $noteEffect->getBend();

    // Set effect bend
    $noteEffect->setBend($effectBend);

------------------------------------------------------------------------

EffectGrace
===========

You may handle its :ref:`EffectGrace <api.music.effectgrace>`.

.. code-block:: php

    // Get effect grace
    $effectGrace = $noteEffect->getGrace();

    // Set effect grace
    $noteEffect->setGrace($effectGrace);

------------------------------------------------------------------------

EffectHarmonic
==============

You may handle its :ref:`EffectHarmonic <api.music.effectharmonic>`.

.. code-block:: php

    // Get effect harmonic
    $effectHarmonic = $noteEffect->getHarmonic();

    // Set effect harmonic
    $noteEffect->setHarmonic($effectHarmonic);

------------------------------------------------------------------------

EffectTremoloBar
================

You may handle its :ref:`EffectTremoloBar <api.music.effecttremolobar>`.

.. code-block:: php

    // Get effect tremolo bar
    $effectTremoloBar = $noteEffect->getTremoloBar();

    // Set effect tremolo bar
    $noteEffect->setTremoloBar($effectTremoloBar);

------------------------------------------------------------------------

EffectTremoloPicking
====================

You may handle its :ref:`EffectTremoloPicking <api.music.effecttremolopicking>`.

.. code-block:: php

    // Get effect tremolo picking
    $effectTremoloPicking = $noteEffect->getTremoloPicking();

    // Set effect tremolo picking
    $noteEffect->setTremoloPicking($effectTremoloPicking);

------------------------------------------------------------------------

EffectTrill
===========

You may handle its :ref:`EffectTrill <api.music.effecttrill>`.

.. code-block:: php

    // Get effect trill
    $effectTrill = $noteEffect->getTrill();

    // Set effect trill
    $noteEffect->setTrill($effectTrill);

------------------------------------------------------------------------

Notes
========

You may handle :ref:`notes <api.music.note>`.

.. code-block:: php

    // Number of notes
    $count = $noteEffect->countNotes();

    // Get an array of notes
    $notes = $noteEffect->getNotes();

    // Get a single note by its index
    // starting from 0 to n-1
    $note = $noteEffect->getNote(0);

    // Remove a note
    $noteEffect->removeNote($note);

    // Add a note
    $noteEffect->addNote($note);
