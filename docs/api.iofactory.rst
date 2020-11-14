.. _api.iofactory:

=========
IOFactory
=========

IOFactory is done in order to:

- create empty PhpTabs,
- load PhpTabs from files, strings and arrays


.. code-block:: php

    use PhpTabs\IOFactory;

    $song = IOFactory::create();

    $song = IOFactory::fromArray($array);

    $song = IOFactory::fromFile($filename);

    $song = IOFactory::fromString(``string``, $format);

    $song = IOFactory::fromJsonFile($jsonFile);

    $song = IOFactory::fromSerializedFile($phpSerializedFilename);

    $song = IOFactory::fromJson($jsonString);

    $song = IOFactory::fromSerialized($phpSerializedString);


All these methods return a ``PhpTabs`` instance.

------------------------------------------------------------------------

create()
========

This method returns an empty ``PhpTabs`` instance.

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Equivalent to 'new PhpTabs()'
    $tab = IOFactory::create();

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=0"


------------------------------------------------------------------------

fromArray($data)
================

This method returns a ``PhpTabs`` resource, loaded from a 
PHP array.

Parameters
----------

*array* ``$data`` An array previously exported with ``$phptabs->toArray()``

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create an empty tabs
    $tab = IOFactory::create();

    // Export as an array
    $data = $tab->export();

    // Now you can reimport as an array
    $tab = IOFactory::fromArray($data);

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=0"

------------------------------------------------------------------------

fromFile($filename, $type)
==========================

This method returns a ``PhpTabs`` instance, loaded from a file.

Parameters
----------

**string** ``$filename`` 
**string** ``$type``  *Optional* 

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create a PhpTabs instance
    $tab = IOFactory::fromFile('mytabs.gp4');

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=2"


In case you need to force a parser type, use the second parameter.

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create a PhpTabs instance from a JSON file
    $tab = IOFactory::fromFile('mytabs.dat', 'json');

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=2"

------------------------------------------------------------------------

fromJsonFile($filename)
=======================

This method returns a ``PhpTabs`` resource, loaded from a JSON file.

Parameters
----------

**string** ``$filename``

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create a PhpTabs instance
    $tab = IOFactory::fromJsonFile('mytabs.json');

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=2"

------------------------------------------------------------------------

fromSerializedFile($filename)
=============================

This method returns a ``PhpTabs`` resource, loaded from a PHP serialized
file.

Parameters
----------

**string** ``$filename`` 

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create a PhpTabs instance
    $tab = IOFactory::fromSerializedFile('mytabs.ser');

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=2"

------------------------------------------------------------------------

fromJson($string)
=================

This method returns a ``PhpTabs`` instance loaded from a JSON string.

Parameters
----------

**string** ``string`` 

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create a PhpTabs instance
    $tab = IOFactory::fromJson('{"song":{"name":null,"artist":null,"album":null,"author":null,"copyright":null,"writer":null,"comments":null,"channels":[],"measureHeaders":[],"tracks":[]}}');

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=0"

------------------------------------------------------------------------

fromSerialized($string)
=======================

This method returns a ``PhpTabs`` instance, loaded from a PHP serialized
string.

Parameters
----------

**string** ``string`` 

Type
----

``PhpTabs\PhpTabs``

Example
-------

.. code-block:: php

    use PhpTabs\IOFactory;

    // Create a PhpTabs instance
    $tab = IOFactory::fromSerialized('a:1:{s:4:"song";a:10:{s:4:"name";N;s:6:"artist";N;s:5:"album";N;s:6:"author";N;s:9:"copyright";N;s:6:"writer";N;s:8:"comments";N;s:8:"channels";a:0:{}s:14:"measureHeaders";a:0:{}s:6:"tracks";a:0:{}}}');

    // Print track number
    echo "Track count=" . $tab->countTracks();

    // Should return "Track count=0"
