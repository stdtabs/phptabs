.. _convert-methods:

===============
Convert methods
===============


Phptabs::convert($format)
=========================

Sometimes, to debug or store content another way, you may want to output
a converted string to a variable.

You may make an explicit conversion with the `convert` method.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';

    // The Guitar Pro file is parsed, converted and returned as MIDI
    // content
    $midi = IOFactory::fromFile($filename)->convert('mid');


The following parameters are available:

- `gp3` for Guitar Pro 3
- `gp4` for Guitar Pro 4
- `gp5` for Guitar Pro 5
- `mid` or `midi` for MIDI
- `json`
- `xml`
- `ser` for PHP serialized string
- `txt` or `text` for a textual representation
- `yml` or `yaml`


Shortcuts
=========

There are some shortcuts to do that.

.. code-block:: php

    use PhpTabs\IOFactory;

    $filename = 'my-file.gp5';
    $song = IOFactory::fromFile($filename);

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

