.. _export.variables:

===================
Export to variables
===================


Phptabs::convert($format)
=========================

Sometimes, for debugging or storing contents another way, you may want
to output a song to a variable.

You may make an explicit conversion with the ``convert()`` method.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // The Guitar Pro file is parsed, converted and returned as MIDI
    // content
    $midi = IOFactory::fromFile($filename)->convert('mid');


The following parameters are available:

- ``gp3`` for Guitar Pro 3
- ``gp4`` for Guitar Pro 4
- ``gp5`` for Guitar Pro 5
- ``mid`` or ``midi`` for MIDI
- ``json``
- ``xml``
- ``ser`` for PHP serialized string
- ``txt`` or ``text`` for a textual representation
- ``yml`` or ``yaml``

________________________________________________________________________

PhpTabs shortcuts
=================

There are some shortcuts to do that.

.. code-block:: php

    use PhpTabs\PhpTabs;

    $filename = 'my-file.gp5';
    $song = new PhpTabs($filename);

    // Guitar Pro 3
    $gp3 = $song->toGuitarPro3();

    // Guitar Pro 4
    $gp4 = $song->toGuitarPro4();

    // Guitar Pro 5
    $gp5 = $song->toGuitarPro5();

    // MIDI
    $midi = $song->toMidi();

    // JSON
    $json = $song->toJson();

    // XML
    $xml = $song->toXml();

    // YAML
    $yml = $song->toYaml();

    // Text
    $txt = $song->toText();

    // PHP Serialized
    $ser = $song->toSerialized();


All these methods return strings (binary or not).

________________________________________________________________________

PHP array
=========

You may export a whole song as a PHP array with the ``toArray()``
method.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';
    $song = IOFactory::fromFile($filename);

    $array = $song->toArray();


Exports are made to visualize the internal music-tree or to communicate
with a third-party application.

Exported arrays may be imported with ``fromArray()`` method.

.. code-block:: php

    use PhpTabs\IOFactory;

    $song = IOFactory::fromArray($array);


This way of reading data is bypassing entire parsing and may lead to
better performances for large files.

For those who are interested, there is a
:ref:`manual dedicated to performances <ex.performance-caching>` issues.

.. warning ::
    All modifications that you made before a ``fromArray()`` call will
    be erased, including meta informations.
