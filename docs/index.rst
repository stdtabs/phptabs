.. title:: PhpTabs manual
.. meta::
   :description: PhpTabs is a PHP library for reading, writing and rendering Guitar Pro tabs and MIDI files.
   :keywords: PhpTabs GuitarPro tabs MIDI parsing rendering
   :author: Landrok


=====================
PhpTabs Documentation
=====================

|latest-stable| |build-status| |license|

PhpTabs is a PHP library for reading, writing and rendering tabs and
MIDI files.

It provides direct methods to :ref:`read a song name <api.phptabs>`,
get a list of instruments or whatever be your needs.

Phptabs is built on the top of a :ref:`music stack <ref.music-model>` that
lets you create or modify your songs.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    // Display some metadata
    echo $song->getName();

    // Display the number of measures
    // for the first track
    echo $song->getTrack(0)->countMeasures();


.. toctree::
    :maxdepth: 2

    overview


.. toctree::
    :caption: Parse and save songs
    :maxdepth: 1

    parse.files
    parse.strings
    export.files
    export.variables


.. toctree::
    :caption: Traverse and slice songs
    :maxdepth: 1

    traverse.songs
    target-track-measure
    slice-tracks-measures
    

.. toctree::
    :caption: Render songs
    :maxdepth: 1

    render.songs
    renderer.ascii
    renderer.vextab

.. toctree::
    :caption: Examples
    :maxdepth: 1

    ex.calculate-measure-and-beat-durations-in-seconds
    ex.tabs-from-scratch
    ex.performance-caching


.. toctree::
    :caption: Reference
    :maxdepth: 1

    ref.architecture
    ref.iofactory
    api.phptabs
    ref.music-model



.. |build-status| image:: https://api.travis-ci.org/stdtabs/phptabs.svg?branch=master
    :alt: Build status
    :target: https://travis-ci.org/stdtabs/phptabs

.. |latest-stable| image:: https://poser.pugx.org/stdtabs/phptabs/version.svg
    :alt: Latest
    :target: https://github.com/stdtabs/phptabs/releases

.. |license| image:: https://poser.pugx.org/stdtabs/phptabs/license.svg
    :alt: License
    :target: https://packagist.org/packages/stdtabs/phptabs
