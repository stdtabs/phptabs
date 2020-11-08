.. title:: PhpTabs manual
.. :description: PhpTabs is a PHP library for reading, writing and rendering Guitar Pro tabs and MIDI files.

=====================
PhpTabs Documentation
=====================

|latest-stable| |build-status| |license|

PhpTabs is a PHP library for reading, writing and rendering tabs and
MIDI files.

It provides direct methods to :ref:`read a song name <api.music-song>`,
get a list of instruments or whatever be your needs.

Phptabs is built on the top of a :ref:`music stack <api.phptabs>` that
lets you create or modify your songs.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';

    $song = new PhpTabs($filename);

    echo $song->getName();


User Guide
==========

.. toctree::
    :maxdepth: 3

    overview
    read.parse-files
    read.parse-strings
    record-files
    convert-methods
    render-tabs-and-songs
    exporting-tabs

    slicing-songs
    traversing-song-model
    tabs-from-scratch
    architecture
    iofactory
    renderer.vextab
    renderer.ascii
    api-reference
    
________________________________________________________________________

.. |build-status| image:: https://api.travis-ci.org/stdtabs/phptabs.svg?branch=master
    :alt: Build status
    :target: https://travis-ci.org/stdtabs/phptabs

.. |latest-stable| image:: https://poser.pugx.org/stdtabs/phptabs/version.svg
    :alt: Latest
    :target: https://github.com/stdtabs/phptabs/releases

.. |license| image:: https://poser.pugx.org/stdtabs/phptabs/license.svg
    :alt: License
    :target: https://packagist.org/packages/stdtabs/phptabs
