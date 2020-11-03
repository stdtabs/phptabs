.. title:: PhpTabs manual

=====================
PhpTabs Documentation
=====================

|latest-stable| |build-status| |license|

PhpTabs is a PHP library for reading, writing and rendering tabs and
MIDI files.

It provides direct methods to [read a song name](music-song.html#getname),
get a list of instruments or whatever be your needs.

Phptabs is built on the top of a [music stack](phptabs.html) that lets
you create or modify your songs.

- [Features](#features)
- [Manual](#manual)
- [Supported formats](#supported-formats)
- [Requirements](#requirements)
- [Contribution and Support](#contribution-and-support)
- [License](#license)

------------------------------------------------------------------------

## Features

- [Read](basics.html#read-from-a-file), [convert](basics.html#convert) and [write](basics.html#save-to-a-file) GuitarPro/MIDI files
- [Traverse](basics.html#traversing) and CRUD metadata and data
- [Export](basics.html#export-data) data to JSON, XML, YAML and other formats
- [Import](basics.html#import-data) data from PHP arrays, JSON & serialized strings
- Web-rendering in [VexTab notation](render-as-vextab.html)
- Web-rendering as an [ASCII tablature](render-as-an-ascii-tab.html)
- [Calculate durations in seconds](calculate-measure-and-beat-durations-in-seconds.html)

------------------------------------------------------------------------

## Manual

- [Getting started](/getting-started.html)

- [Basics](basics.html)
  - [Read a file](basics.html#read-from-a-file)
  - [Save to a file](basics.html#save-to-a-file)
  - [Export](basics.html#export-data)
  - [Import](basics.html#import-data)
  - [Render](basics.html#render)
  - [Architecture](basics.html#architecture)
  - [Traversing](basics.html#traversing)


- [Music-Object-Model overview](phptabs.html)
  - [Song](music-song.html)
    - [Track](music-track.html)

- Some use-cases examples:

  - Read and update song metadata (Name, author)

  - [Render a track as a VexTab string](render-as-vextab.html)
  
  - [Render a track as an ASCII tablature](render-as-an-ascii-tab.html)

  - [Calculate durations in seconds](calculate-measure-and-beat-durations-in-seconds.html)

  - Read measures for a particular track

  - Read notes for a particular measure

  - [Create a tab from scratch (An empty song)](create-a-tablature-from-scratch.html)

------------------------------------------------------------------------

## Supported formats

PhpTabs currently supports the following file formats:

- GuitarPro 3 (.gp3)
- GuitarPro 4 (.gp4)
- GuitarPro 5 (.gp5)
- MIDI files (.mid, .midi)

------------------------------------------------------------------------

## Requirements

Support for PHP 7.2+ and 8.0

------------------------------------------------------------------------

## Contribution and Support

If you have any questions, please [open an issue]({{ site.github_repository_url }}/issues).

You want to write another parser, to fix a bug? Please open [a pull request]({{ site.github_repository_url }}).

------------------------------------------------------------------------

## License

PhpTabs is licensed under [LGPL2.1+]({{ site.github_repository_url }}/blob/master/LICENSE).


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
